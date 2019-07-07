<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\LazyHandlerRegistry;
use JMS\Serializer\SerializerInterface;

abstract class LazyHandlerRegistryTest extends HandlerRegistryTest
{
    protected $container;

    protected function setUp()
    {
        $this->container = $this->createContainer();

        parent::setUp();
    }

    protected function createHandlerRegistry()
    {
        return new LazyHandlerRegistry($this->container);
    }

    public function testRegisteredHandlersCanBeRetrievedWhenBeingDefinedAsServices()
    {
        $jsonSerializationHandler = new HandlerService();
        $this->registerHandlerService('handler.serialization.json', $jsonSerializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', SerializerInterface::FORMAT_JSON, ['handler.serialization.json', 'handle']);

        $jsonDeserializationHandler = new HandlerService();
        $this->registerHandlerService('handler.deserialization.json', $jsonDeserializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', SerializerInterface::FORMAT_JSON, ['handler.deserialization.json', 'handle']);

        $xmlSerializationHandler = new HandlerService();
        $this->registerHandlerService('handler.serialization.xml', $xmlSerializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', SerializerInterface::FORMAT_XML, ['handler.serialization.xml', 'handle']);

        $xmlDeserializationHandler = new HandlerService();
        $this->registerHandlerService('handler.deserialization.xml', $xmlDeserializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', SerializerInterface::FORMAT_XML, ['handler.deserialization.xml', 'handle']);

        self::assertSame([$jsonSerializationHandler, 'handle'], $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', SerializerInterface::FORMAT_JSON));
        self::assertSame([$jsonDeserializationHandler, 'handle'], $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', SerializerInterface::FORMAT_JSON));
        self::assertSame([$xmlSerializationHandler, 'handle'], $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', SerializerInterface::FORMAT_XML));
        self::assertSame([$xmlDeserializationHandler, 'handle'], $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', SerializerInterface::FORMAT_XML));
    }

    abstract protected function createContainer();

    abstract protected function registerHandlerService($serviceId, $listener);
}

class HandlerService
{
    public function handle()
    {
    }
}
