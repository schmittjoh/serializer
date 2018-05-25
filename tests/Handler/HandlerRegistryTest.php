<?php

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\HandlerRegistry;

class HandlerRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $handlerRegistry;

    protected function setUp()
    {
        $this->handlerRegistry = $this->createHandlerRegistry();
    }

    public function testRegisteredHandlersCanBeRetrieved()
    {
        $jsonSerializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_SERIALIZATION, '\stdClass', 'json', $jsonSerializationHandler);

        $jsonDeserializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_DESERIALIZATION, '\stdClass', 'json', $jsonDeserializationHandler);

        $xmlSerializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_SERIALIZATION, '\stdClass', 'xml', $xmlSerializationHandler);

        $xmlDeserializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_DESERIALIZATION, '\stdClass', 'xml', $xmlDeserializationHandler);

        $this->assertSame($jsonSerializationHandler, $this->handlerRegistry->getHandler(GraphNavigator::DIRECTION_SERIALIZATION, '\stdClass', 'json'));
        $this->assertSame($jsonDeserializationHandler, $this->handlerRegistry->getHandler(GraphNavigator::DIRECTION_DESERIALIZATION, '\stdClass', 'json'));
        $this->assertSame($xmlSerializationHandler, $this->handlerRegistry->getHandler(GraphNavigator::DIRECTION_SERIALIZATION, '\stdClass', 'xml'));
        $this->assertSame($xmlDeserializationHandler, $this->handlerRegistry->getHandler(GraphNavigator::DIRECTION_DESERIALIZATION, '\stdClass', 'xml'));
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
