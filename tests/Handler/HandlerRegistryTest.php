<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use PHPUnit\Framework\TestCase;

class HandlerRegistryTest extends TestCase
{
    /**
     * @var HandlerRegistryInterface
     */
    protected $handlerRegistry;

    protected function setUp()
    {
        $this->handlerRegistry = $this->createHandlerRegistry();
    }

    public function testRegisteredHandlersCanBeRetrieved()
    {
        $jsonSerializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'json', $jsonSerializationHandler);

        $jsonDeserializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'json', $jsonDeserializationHandler);

        $xmlSerializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'xml', $xmlSerializationHandler);

        $xmlDeserializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'xml', $xmlDeserializationHandler);

        self::assertSame($jsonSerializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'json'));
        self::assertSame($jsonDeserializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'json'));
        self::assertSame($xmlSerializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'xml'));
        self::assertSame($xmlDeserializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'xml'));
    }

    public function testSerializeClassHierarchy(): void
    {
        $handler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, DummyParent::class, 'json', $handler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, DummyParent::class, 'json', $handler);

        self::assertSame($handler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, DummyChild::class, 'json'));
        self::assertSame($handler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, DummyChild::class, 'json'));
    }

    public function testSerializeInterface(): void
    {
        $handler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, SomeInterface::class, 'json', $handler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, SomeInterface::class, 'json', $handler);

        self::assertSame($handler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, SomeImpl::class, 'json'));
        self::assertSame($handler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, SomeImpl::class, 'json'));
    }

    protected function createHandlerRegistry()
    {
        return new HandlerRegistry();
    }
}

class DummyHandler
{
    public function __call($name, $arguments)
    {
    }
}

class DummyParent
{
    public $value;
}

class DummyChild extends DummyParent
{
}

interface SomeInterface
{
}

class SomeImpl implements SomeInterface
{
}
