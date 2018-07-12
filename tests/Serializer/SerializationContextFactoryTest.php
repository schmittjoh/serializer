<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Visitor\Factory\JsonDeserializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use Metadata\MetadataFactory;
use PHPUnit\Framework\TestCase;

class SerializationContextFactoryTest extends TestCase
{
    protected $serializer;
    protected $metadataFactory;
    protected $handlerRegistry;
    protected $unserializeObjectConstructor;
    protected $serializationVisitors;
    protected $deserializationVisitors;

    public function setUp()
    {
        parent::setUp();

        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $this->metadataFactory = new MetadataFactory(new AnnotationDriver(new AnnotationReader(), $namingStrategy));
        $this->handlerRegistry = new HandlerRegistry();
        $this->unserializeObjectConstructor = new UnserializeObjectConstructor();

        $this->serializationVisitors = ['json' => new JsonSerializationVisitorFactory()];
        $this->deserializationVisitors = ['json' => new JsonDeserializationVisitorFactory()];
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

        $builder = SerializerBuilder::create();
        $builder->setSerializationContextFactory($contextFactoryMock);
        $serializer = $builder->build();

        $result = $serializer->serialize(['value' => null], 'json');

        self::assertEquals('{"value":null}', $result);
    }

    public function testDeserializeUseProvidedDeserializationContext()
    {
        $contextFactoryMock = $this->getMockForAbstractClass('JMS\\Serializer\\ContextFactory\\DeserializationContextFactoryInterface');
        $context = new DeserializationContext();

        $contextFactoryMock
            ->expects($this->once())
            ->method('createDeserializationContext')
            ->will($this->returnValue($context));

        $builder = SerializerBuilder::create();
        $builder->setDeserializationContextFactory($contextFactoryMock);
        $serializer = $builder->build();

        $result = $serializer->deserialize('{"value":null}', 'array', 'json');

        self::assertEquals(['value' => null], $result);
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

        $builder = SerializerBuilder::create();
        $builder->setSerializationContextFactory($contextFactoryMock);
        $serializer = $builder->build();

        $result = $serializer->toArray(['value' => null]);

        self::assertEquals(['value' => null], $result);
    }

    public function testFromArrayUseProvidedDeserializationContext()
    {
        $contextFactoryMock = $this->getMockForAbstractClass('JMS\\Serializer\\ContextFactory\\DeserializationContextFactoryInterface');
        $context = new DeserializationContext();

        $contextFactoryMock
            ->expects($this->once())
            ->method('createDeserializationContext')
            ->will($this->returnValue($context));

        $builder = SerializerBuilder::create();
        $builder->setDeserializationContextFactory($contextFactoryMock);
        $serializer = $builder->build();

        $result = $serializer->fromArray(['value' => null], 'array');

        self::assertEquals(['value' => null], $result);
    }
}
