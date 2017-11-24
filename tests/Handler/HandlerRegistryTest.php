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
