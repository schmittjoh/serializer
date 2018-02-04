<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\ContextualNamingStrategy;
use JMS\Serializer\Tests\Fixtures\Person;
use JMS\Serializer\Tests\Fixtures\PersonSecret;
use JMS\Serializer\Tests\Fixtures\PersonSecretWithVariables;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Filesystem\Filesystem;

class SerializerBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var SerializerBuilder */
    private $builder;
    private $fs;
    private $tmpDir;

    public function testBuildWithoutAnythingElse()
    {
        $serializer = $this->builder->build();

        $this->assertEquals('"foo"', $serializer->serialize('foo', 'json'));
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>
<result><![CDATA[foo]]></result>
', $serializer->serialize('foo', 'xml'));
        $this->assertEquals('foo
', $serializer->serialize('foo', 'yml'));

        $this->assertEquals('foo', $serializer->deserialize('"foo"', 'string', 'json'));
        $this->assertEquals('foo', $serializer->deserialize('<?xml version="1.0" encoding="UTF-8"?><result><![CDATA[foo]]></result>', 'string', 'xml'));
    }

    public function testWithCache()
    {
        $this->assertFileNotExists($this->tmpDir);

        $this->assertSame($this->builder, $this->builder->setCacheDir($this->tmpDir));
        $serializer = $this->builder->build();

        $this->assertFileExists($this->tmpDir);
        $this->assertFileExists($this->tmpDir . '/annotations');
        $this->assertFileExists($this->tmpDir . '/metadata');

        $factory = $this->getField($serializer, 'factory');
        $this->assertAttributeSame(false, 'debug', $factory);
        $this->assertAttributeNotSame(null, 'cache', $factory);
    }

    public function testDoesAddDefaultHandlers()
    {
        $serializer = $this->builder->build();

        $this->assertEquals('"2020-04-16T00:00:00+0000"', $serializer->serialize(new \DateTime('2020-04-16', new \DateTimeZone('UTC')), 'json'));
    }

    public function testDoesNotAddDefaultHandlersWhenExplicitlyConfigured()
    {
        $this->assertSame($this->builder, $this->builder->configureHandlers(function (HandlerRegistry $registry) {
        }));

        $this->assertEquals('{}', $this->builder->build()->serialize(new \DateTime('2020-04-16'), 'json'));
    }

    /**
     * @expectedException JMS\Serializer\Exception\UnsupportedFormatException
     * @expectedExceptionMessage The format "xml" is not supported for serialization.
     */
    public function testDoesNotAddOtherVisitorsWhenConfiguredExplicitly()
    {
        $this->assertSame(
            $this->builder,
            $this->builder->setSerializationVisitor('json', new JsonSerializationVisitor(new CamelCaseNamingStrategy()))
        );

        $this->builder->build()->serialize('foo', 'xml');
    }

    public function testIncludeInterfaceMetadata()
    {
        $this->assertFalse(
            $this->getIncludeInterfaces($this->builder),
            'Interface metadata are not included by default'
        );

        $this->assertTrue(
            $this->getIncludeInterfaces($this->builder->includeInterfaceMetadata(true)),
            'Force including interface metadata'
        );

        $this->assertFalse(
            $this->getIncludeInterfaces($this->builder->includeInterfaceMetadata(false)),
            'Force not including interface metadata'
        );

        $this->assertSame(
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

        $result = $serializer->serialize(array('value' => null), 'json');

        $this->assertEquals('{"value":null}', $result);
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

        $this->assertEquals(array('value' => null), $result);
    }

    public function testSetCallbackSerializationContextWithSerializeNull()
    {
        $this->builder->setSerializationContextFactory(function () {
            return SerializationContext::create()
                ->setSerializeNull(true);
        });

        $serializer = $this->builder->build();

        $result = $serializer->serialize(array('value' => null), 'json');

        $this->assertEquals('{"value":null}', $result);
    }

    public function testSetCallbackSerializationContextWithNotSerializeNull()
    {
        $this->builder->setSerializationContextFactory(function () {
            return SerializationContext::create()
                ->setSerializeNull(false);
        });

        $serializer = $this->builder->build();

        $result = $serializer->serialize(array('value' => null, 'not_null' => 'ok'), 'json');

        $this->assertEquals('{"not_null":"ok"}', $result);
    }

    public function expressionFunctionProvider()
    {
        return [
            [
                new ExpressionFunction('show_data', function () {
                    return "true";
                }, function () {
                    return true;
                }),
                '{"name":"mike"}'
            ],
            [
                new ExpressionFunction('show_data', function () {
                    return "false";
                }, function () {
                    return false;
                }),
                '{"name":"mike","gender":"f"}'
            ]
        ];
    }

    /**
     * @dataProvider expressionFunctionProvider
     * @param ExpressionFunction $function
     * @param $json
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

        $this->assertEquals($json, $serializer->serialize($person, 'json'));
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
        $this->assertEquals('{"name":"mike","gender":"f"}', $serialized);

        $object = $serializer->deserialize($serialized, PersonSecretWithVariables::class, 'json');
        $this->assertEquals($person, $object);
    }

    public function testAdvancedNamingStrategy()
    {
        $this->builder->setAdvancedNamingStrategy(new ContextualNamingStrategy());
        $serializer = $this->builder->build();

        $person = new Person();
        $person->name = "bar";

        $json = $serializer->serialize($person, "json");
        $this->assertEquals('{"NAME":"bar"}', $json);

        $json = '{"Name": "bar"}';
        $person = $serializer->deserialize($json, Person::class, "json");
        $this->assertEquals("bar", $person->name);
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
