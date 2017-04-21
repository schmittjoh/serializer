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

namespace JMS\Serializer\Tests\Twig;

use JMS\Serializer\Twig\SerializerExtension;
use JMS\Serializer\Twig\SerializerRuntimeExtension;
use JMS\Serializer\Twig\SerializerRuntimeHelper;

class SerializerExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $mockSerializer = $this->getMock('JMS\Serializer\SerializerInterface');
        $obj = new \stdClass();
        $mockSerializer
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($obj), $this->equalTo('json'));
        $serializerExtension = new SerializerExtension($mockSerializer);
        $serializerExtension->serialize($obj);

        $this->assertEquals('jms_serializer', $serializerExtension->getName());

        $filters = $serializerExtension->getFilters();
        $this->assertInstanceOf('Twig_SimpleFilter', $filters[0]);
        $this->assertSame(array($serializerExtension, 'serialize'), $filters[0]->getCallable());

        $this->assertEquals(
            array(new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create')),
            $serializerExtension->getFunctions()
        );
    }

    public function testRuntimeSerializerHelper()
    {
        $obj = new \stdClass();

        $mockSerializer = $this->getMock('JMS\Serializer\SerializerInterface');
        $mockSerializer
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($obj), $this->equalTo('json'));

        $serializerExtension = new SerializerRuntimeHelper($mockSerializer);
        $serializerExtension->serialize($obj);
    }

    public function testRuntimeSerializerExtension()
    {
        $serializerExtension = new SerializerRuntimeExtension();

        $this->assertEquals('jms_serializer', $serializerExtension->getName());
        $this->assertEquals(
            array(new \Twig_SimpleFilter('serialize', array(SerializerRuntimeHelper::class, 'serialize'))),
            $serializerExtension->getFilters()
        );
        $this->assertEquals(
            array(new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create')),
            $serializerExtension->getFunctions()
        );
    }
}
