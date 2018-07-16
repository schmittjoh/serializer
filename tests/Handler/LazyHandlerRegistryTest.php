<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\LazyHandlerRegistry;

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
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'json', ['handler.serialization.json', 'handle']);

        $jsonDeserializationHandler = new HandlerService();
        $this->registerHandlerService('handler.deserialization.json', $jsonDeserializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'json', ['handler.deserialization.json', 'handle']);

        $xmlSerializationHandler = new HandlerService();
        $this->registerHandlerService('handler.serialization.xml', $xmlSerializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'xml', ['handler.serialization.xml', 'handle']);

        $xmlDeserializationHandler = new HandlerService();
        $this->registerHandlerService('handler.deserialization.xml', $xmlDeserializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'xml', ['handler.deserialization.xml', 'handle']);

        self::assertSame([$jsonSerializationHandler, 'handle'], $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'json'));
        self::assertSame([$jsonDeserializationHandler, 'handle'], $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'json'));
        self::assertSame([$xmlSerializationHandler, 'handle'], $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'xml'));
        self::assertSame([$xmlDeserializationHandler, 'handle'], $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'xml'));
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
