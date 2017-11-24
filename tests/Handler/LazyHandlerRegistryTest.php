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
