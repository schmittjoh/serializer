<?php

namespace JMS\Serializer\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Metadata\MetadataFactory;
use PhpCollection\Map;

class SerializationContextFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $serializer;

    public function setUp()
    {
        parent::setUp();

        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());

        $this->serializer = new Serializer(
            new MetadataFactory(new AnnotationDriver(new AnnotationReader())),
            new HandlerRegistry(),
            new UnserializeObjectConstructor(),
            new Map(array('json' => new JsonSerializationVisitor($namingStrategy))),
            new Map(array('json' => new JsonDeserializationVisitor($namingStrategy)))
        );
    }

    public function testSerializeUseProvidedSerializationContext()
    {
        $contextFactoryMock = $this->getMockForAbstractClass('JMS\\Serializer\\ContextFactory\\SerializationContextFactoryInterface');
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $contextFactoryMock
            ->expects($this->once())
            ->method('createSerializationContext')
            ->will($this->returnValue($context));

        $this->serializer->setSerializationContextFactory($contextFactoryMock);

        $result = $this->serializer->serialize(array('value' => null), 'json');

        $this->assertEquals('{"value":null}', $result);
    }

    public function testDeserializeUseProvidedDeserializationContext()
    {
        $contextFactoryMock = $this->getMockForAbstractClass('JMS\\Serializer\\ContextFactory\\DeserializationContextFactoryInterface');
        $context = new DeserializationContext();

        $contextFactoryMock
            ->expects($this->once())
            ->method('createDeserializationContext')
            ->will($this->returnValue($context));

        $this->serializer->setDeserializationContextFactory($contextFactoryMock);

        $result = $this->serializer->deserialize('{"value":null}', 'array', 'json');

        $this->assertEquals(array('value' => null), $result);
    }

    public function testToArrayUseProvidedSerializationContext()
    {
        $contextFactoryMock = $this->getMockForAbstractClass('JMS\\Serializer\\ContextFactory\\SerializationContextFactoryInterface');
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $contextFactoryMock
            ->expects($this->once())
            ->method('createSerializationContext')
            ->will($this->returnValue($context));

        $this->serializer->setSerializationContextFactory($contextFactoryMock);

        $result = $this->serializer->toArray(array('value' => null));

        $this->assertEquals(array('value' => null), $result);
    }

    public function testFromArrayUseProvidedDeserializationContext()
    {
        $contextFactoryMock = $this->getMockForAbstractClass('JMS\\Serializer\\ContextFactory\\DeserializationContextFactoryInterface');
        $context = new DeserializationContext();

        $contextFactoryMock
            ->expects($this->once())
            ->method('createDeserializationContext')
            ->will($this->returnValue($context));

        $this->serializer->setDeserializationContextFactory($contextFactoryMock);

        $result = $this->serializer->fromArray(array('value' => null), 'array');

        $this->assertEquals(array('value' => null), $result);
    }
}
