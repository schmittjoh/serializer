<?php

namespace JMS\Serializer\Tests;

use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;

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
        $this->assertFileExists($this->tmpDir.'/annotations');
        $this->assertFileExists($this->tmpDir.'/metadata');

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
        $this->assertSame($this->builder, $this->builder->configureHandlers(function(HandlerRegistry $registry) {
        }));

        $this->assertEquals('[]', $this->builder->build()->serialize(new \DateTime('2020-04-16'), 'json'));
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

    protected function setUp()
    {
        $this->builder = SerializerBuilder::create();
        $this->fs = new Filesystem();

        $this->tmpDir = sys_get_temp_dir().'/serializer';
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
