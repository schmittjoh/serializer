<?php

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\GraphNavigator;
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
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_SERIALIZATION, '\stdClass', 'json', array('handler.serialization.json', 'handle'));

        $jsonDeserializationHandler = new HandlerService();
        $this->registerHandlerService('handler.deserialization.json', $jsonDeserializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_DESERIALIZATION, '\stdClass', 'json', array('handler.deserialization.json', 'handle'));

        $xmlSerializationHandler = new HandlerService();
        $this->registerHandlerService('handler.serialization.xml', $xmlSerializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_SERIALIZATION, '\stdClass', 'xml', array('handler.serialization.xml', 'handle'));

        $xmlDeserializationHandler = new HandlerService();
        $this->registerHandlerService('handler.deserialization.xml', $xmlDeserializationHandler);
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_DESERIALIZATION, '\stdClass', 'xml', array('handler.deserialization.xml', 'handle'));

        $this->assertSame(array($jsonSerializationHandler, 'handle'), $this->handlerRegistry->getHandler(GraphNavigator::DIRECTION_SERIALIZATION, '\stdClass', 'json'));
        $this->assertSame(array($jsonDeserializationHandler, 'handle'), $this->handlerRegistry->getHandler(GraphNavigator::DIRECTION_DESERIALIZATION, '\stdClass', 'json'));
        $this->assertSame(array($xmlSerializationHandler, 'handle'), $this->handlerRegistry->getHandler(GraphNavigator::DIRECTION_SERIALIZATION, '\stdClass', 'xml'));
        $this->assertSame(array($xmlDeserializationHandler, 'handle'), $this->handlerRegistry->getHandler(GraphNavigator::DIRECTION_DESERIALIZATION, '\stdClass', 'xml'));
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
