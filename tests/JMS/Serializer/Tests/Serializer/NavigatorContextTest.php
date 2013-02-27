<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Tests\Fixtures\Node;
use JMS\Serializer\SerializerBuilder;

class NavigatorContextTest extends \PHPUnit_Framework_TestCase
{
    public function testNavigatorContextPathAndDepth()
    {
        $object = new Node(array(
            new Node(),
            new Node(array(
                new Node()
            )),
        ));
        $objects = array($object, $object->children[0], $object->children[1], $object->children[1]->children[0]);

        $self = $this;

        $exclusionStrategy = $this->getMock('JMS\Serializer\Exclusion\ExclusionStrategyInterface');
        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipClass')
            ->with($this->anything(), $this->callback(function ($navigatorContext) use ($self, $objects) {
                        $expectedDepth = $expectedPath = null;

                        if ($navigatorContext->getObject() === $objects[0]) {
                            $expectedDepth = 1;
                            $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node';
                        } elseif ($navigatorContext->getObject() === $objects[1]) {
                            $expectedDepth = 2;
                            $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                        } elseif ($navigatorContext->getObject() === $objects[2]) {
                            $expectedDepth = 2;
                            $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                        } elseif ($navigatorContext->getObject() === $objects[3]) {
                            $expectedDepth = 3;
                            $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                        }

                        $self->assertEquals($expectedDepth, $navigatorContext->getDepth(), 'shouldSkipClass depth');
                        $self->assertEquals($expectedPath, $navigatorContext->getPath(), 'shouldSkipClass path');

                        return true;
                    }))
            ->will($this->returnValue(false));

        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipProperty')
            ->with($this->anything(), $this->callback(function ($navigatorContext) use ($self, $objects) {
                        $expectedDepth = $expectedPath = null;

                        if ($navigatorContext->getObject() === $objects[0]) {
                            $expectedDepth = 1;
                            $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node';
                        } elseif ($navigatorContext->getObject() === $objects[1]) {
                            $expectedDepth = 2;
                            $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                        } elseif ($navigatorContext->getObject() === $objects[2]) {
                            $expectedDepth = 2;
                            $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                        } elseif ($navigatorContext->getObject() === $objects[3]) {
                            $expectedDepth = 3;
                            $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                        }

                        $self->assertEquals($expectedDepth, $navigatorContext->getDepth(), 'shouldSkipProperty depth');
                        $self->assertEquals($expectedPath, $navigatorContext->getPath(), 'shouldSkipProperty path');

                        return true;
                    }))
            ->will($this->returnValue(false));

        $serializer = SerializerBuilder::create()->build();

        $serializer->serialize($object, 'json', SerializationContext::create()->addExclusionStrategy($exclusionStrategy));
    }
}
