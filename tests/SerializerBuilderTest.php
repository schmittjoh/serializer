<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\PersonSecret;
use JMS\Serializer\Tests\Fixtures\PersonSecretWithVariables;
use JMS\Serializer\Type\ParserInterface;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Filesystem\Filesystem;

class SerializerBuilderTest extends TestCase
{
    /** @var SerializerBuilder */
    private $builder;
    private $fs;
    private $tmpDir;

    public function testBuildWithoutAnythingElse()
    {
        $serializer = $this->builder->build();

        self::assertEquals('"foo"', $serializer->serialize('foo', 'json'));
        self::assertEquals('<?xml version="1.0" encoding="UTF-8"?>
<result><![CDATA[foo]]></result>
', $serializer->serialize('foo', 'xml'));

        self::assertEquals('foo', $serializer->deserialize('"foo"', 'string', 'json'));
        self::assertEquals('foo', $serializer->deserialize('<?xml version="1.0" encoding="UTF-8"?><result><![CDATA[foo]]></result>', 'string', 'xml'));
    }

    public function testWithCache()
    {
        self::assertFileNotExists($this->tmpDir);

        self::assertSame($this->builder, $this->builder->setCacheDir($this->tmpDir));
        $serializer = $this->builder->build();

        self::assertFileExists($this->tmpDir);
        self::assertFileExists($this->tmpDir . '/annotations');
        self::assertFileExists($this->tmpDir . '/metadata');

        $factory = $this->getField($serializer, 'factory');
        self::assertAttributeSame(false, 'debug', $factory);
        self::assertAttributeNotSame(null, 'cache', $factory);
    }

    public function testDoesAddDefaultHandlers()
    {
        $serializer = $this->builder->build();

        self::assertEquals('"2020-04-16T00:00:00+00:00"', $serializer->serialize(new \DateTime('2020-04-16', new \DateTimeZone('UTC')), 'json'));
    }

    public function testCustomTypeParser()
    {
        $parserMock = $this->getMockBuilder(ParserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parserMock
            ->method('parse')
            ->willReturn(['name' => 'DateTimeImmutable', 'params' => [2 => 'd-Y-m']]);

        $this->builder->setTypeParser($parserMock);

        $serializer = $this->builder->build();

        $result = $serializer->deserialize('"04-2020-10"', 'XXX', 'json');
        self::assertInstanceOf(\DateTimeImmutable::class, $result);
        self::assertEquals('2020-10-04', $result->format('Y-m-d'));
    }

    public function testDoesNotAddDefaultHandlersWhenExplicitlyConfigured()
    {
        self::assertSame($this->builder, $this->builder->configureHandlers(static function (HandlerRegistry $registry) {
        }));

        self::assertEquals('{}', $this->builder->build()->serialize(new \DateTime('2020-04-16'), 'json'));
    }

    /**
     * @expectedException JMS\Serializer\Exception\UnsupportedFormatException
     * @expectedExceptionMessage The format "xml" is not supported for serialization.
     */
    public function testDoesNotAddOtherVisitorsWhenConfiguredExplicitly()
    {
        self::assertSame(
            $this->builder,
            $this->builder->setSerializationVisitor('json', new JsonSerializationVisitorFactory())
        );

        $this->builder->build()->serialize('foo', 'xml');
    }

    public function testIncludeInterfaceMetadata()
    {
        self::assertFalse(
            $this->getIncludeInterfaces($this->builder),
            'Interface metadata are not included by default'
        );

        self::assertTrue(
            $this->getIncludeInterfaces($this->builder->includeInterfaceMetadata(true)),
            'Force including interface metadata'
        );

        self::assertFalse(
            $this->getIncludeInterfaces($this->builder->includeInterfaceMetadata(false)),
            'Force not including interface metadata'
        );

        self::assertSame(
            $this->builder,
            $this->builder->includeInterfaceMetadata(true)
        );
    }

    public function testSetSerializationContext()
    {
        $contextFactoryMock = $this->getMockForAbstractClass('JMS\\Serializer\\ContextFactory\\SerializationContextFactoryInterface');
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $contextFactoryMock
            ->expects($this->once())
            ->method('createSerializationContext')
            ->will($this->returnValue($context));

        $this->builder->setSerializationContextFactory($contextFactoryMock);

        $serializer = $this->builder->build();

        $result = $serializer->serialize(['value' => null], 'json');

        self::assertEquals('{"value":null}', $result);
    }

    public function testSetDeserializationContext()
    {
        $contextFactoryMock = $this->getMockForAbstractClass('JMS\\Serializer\\ContextFactory\\DeserializationContextFactoryInterface');
        $context = new DeserializationContext();

        $contextFactoryMock
            ->expects($this->once())
            ->method('createDeserializationContext')
            ->will($this->returnValue($context));

        $this->builder->setDeserializationContextFactory($contextFactoryMock);

        $serializer = $this->builder->build();

        $result = $serializer->deserialize('{"value":null}', 'array', 'json');

        self::assertEquals(['value' => null], $result);
    }

    public function testSetCallbackSerializationContextWithSerializeNull()
    {
        $this->builder->setSerializationContextFactory(static function () {
            return SerializationContext::create()
                ->setSerializeNull(true);
        });

        $serializer = $this->builder->build();

        $result = $serializer->serialize(['value' => null], 'json');

        self::assertEquals('{"value":null}', $result);
    }

    public function testSetCallbackSerializationContextWithNotSerializeNull()
    {
        $this->builder->setSerializationContextFactory(static function () {
            return SerializationContext::create()
                ->setSerializeNull(false);
        });

        $serializer = $this->builder->build();

        $result = $serializer->serialize(['value' => null, 'not_null' => 'ok'], 'json');

        self::assertEquals('{"not_null":"ok"}', $result);
    }

    public function expressionFunctionProvider()
    {
        return [
            [
                new ExpressionFunction('show_data', static function () {
                    return 'true';
                }, static function () {
                    return true;
                }),
                '{"name":"mike"}',
            ],
            [
                new ExpressionFunction('show_data', static function () {
                    return 'false';
                }, static function () {
                    return false;
                }),
                '{"name":"mike","gender":"f"}',
            ],
        ];
    }

    /**
     * @param ExpressionFunction $function
     * @param string $json
     *
     * @dataProvider expressionFunctionProvider
     */
    public function testExpressionEngine(ExpressionFunction $function, $json)
    {
        $language = new ExpressionLanguage();
        $language->addFunction($function);

        $this->builder->setExpressionEvaluator(new ExpressionEvaluator($language));

        $serializer = $this->builder->build();

        $person = new PersonSecret();
        $person->gender = 'f';
        $person->name = 'mike';

        self::assertEquals($json, $serializer->serialize($person, 'json'));
    }

    public function testExpressionEngineWhenDeserializing()
    {
        $language = new ExpressionLanguage();
        $this->builder->setExpressionEvaluator(new ExpressionEvaluator($language));

        $serializer = $this->builder->build();

        $person = new PersonSecretWithVariables();
        $person->gender = 'f';
        $person->name = 'mike';

        $serialized = $serializer->serialize($person, 'json');
        self::assertEquals('{"name":"mike","gender":"f"}', $serialized);

        $object = $serializer->deserialize($serialized, PersonSecretWithVariables::class, 'json');
        self::assertEquals($person, $object);
    }

    protected function setUp()
    {
        $this->builder = SerializerBuilder::create();
        $this->fs = new Filesystem();

        $this->tmpDir = sys_get_temp_dir() . '/serializer';
        $this->fs->remove($this->tmpDir);
        clearstatcache();
    }

    protected function tearDown()
    {
        $this->fs->remove($this->tmpDir);
    }

    private function getField($obj, $name)
    {
        $ref = new \ReflectionProperty($obj, $name);
        $ref->setAccessible(true);

        return $ref->getValue($obj);
    }

    private function getIncludeInterfaces(SerializerBuilder $builder)
    {
        $factory = $this->getField($builder->build(), 'factory');

        return $this->getField($factory, 'includeInterfaces');
    }
}
