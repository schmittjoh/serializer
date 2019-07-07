<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class HandlerRegistryTest extends TestCase
{
    protected $handlerRegistry;

    protected function setUp()
    {
        $this->handlerRegistry = $this->createHandlerRegistry();
    }

    public function testRegisteredHandlersCanBeRetrieved()
    {
        $jsonSerializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', SerializerInterface::FORMAT_JSON, $jsonSerializationHandler);

        $jsonDeserializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', SerializerInterface::FORMAT_JSON, $jsonDeserializationHandler);

        $xmlSerializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', SerializerInterface::FORMAT_XML, $xmlSerializationHandler);

        $xmlDeserializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', SerializerInterface::FORMAT_XML, $xmlDeserializationHandler);

        self::assertSame($jsonSerializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', SerializerInterface::FORMAT_JSON));
        self::assertSame($jsonDeserializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', SerializerInterface::FORMAT_JSON));
        self::assertSame($xmlSerializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', SerializerInterface::FORMAT_XML));
        self::assertSame($xmlDeserializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', SerializerInterface::FORMAT_XML));
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
