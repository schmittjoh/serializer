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

namespace BDBStudios\Serializer\Tests\Serializer;

use BDBStudios\Serializer\Context;
use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\Metadata\PropertyMetadata;
use BDBStudios\Serializer\SerializationContext;
use BDBStudios\Serializer\Tests\Fixtures\InlineChild;
use BDBStudios\Serializer\Tests\Fixtures\Node;
use BDBStudios\Serializer\SerializerBuilder;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializationContextPathAndDepth()
    {
        $object = new Node(array(
            new Node(),
            new Node(array(
                new Node()
            )),
        ));
        $objects = array($object, $object->children[0], $object->children[1], $object->children[1]->children[0]);

        $self = $this;

        $exclusionStrategy = $this->getMock('BDBStudios\Serializer\Exclusion\ExclusionStrategyInterface');
        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipClass')
            ->with($this->anything(), $this->callback(function (SerializationContext $context) use ($self, $objects) {
                $expectedDepth = $expectedPath = null;

                if ($context->getObject() === $objects[0]) {
                    $expectedDepth = 1;
                    $expectedPath = 'BDBStudios\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[1]) {
                    $expectedDepth = 2;
                    $expectedPath = 'BDBStudios\Serializer\Tests\Fixtures\Node -> BDBStudios\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[2]) {
                    $expectedDepth = 2;
                    $expectedPath = 'BDBStudios\Serializer\Tests\Fixtures\Node -> BDBStudios\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[3]) {
                    $expectedDepth = 3;
                    $expectedPath = 'BDBStudios\Serializer\Tests\Fixtures\Node -> BDBStudios\Serializer\Tests\Fixtures\Node -> BDBStudios\Serializer\Tests\Fixtures\Node';
                }

                $self->assertEquals($expectedDepth, $context->getDepth(), 'shouldSkipClass depth');
                $self->assertEquals($expectedPath, $context->getPath(), 'shouldSkipClass path');

                return true;
            }))
            ->will($this->returnValue(false));

        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipProperty')
            ->with($this->anything(), $this->callback(function (SerializationContext $context) use ($self, $objects) {
                $expectedDepth = $expectedPath = null;

                if ($context->getObject() === $objects[0]) {
                    $expectedDepth = 1;
                    $expectedPath = 'BDBStudios\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[1]) {
                    $expectedDepth = 2;
                    $expectedPath = 'BDBStudios\Serializer\Tests\Fixtures\Node -> BDBStudios\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[2]) {
                    $expectedDepth = 2;
                    $expectedPath = 'BDBStudios\Serializer\Tests\Fixtures\Node -> BDBStudios\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[3]) {
                    $expectedDepth = 3;
                    $expectedPath = 'BDBStudios\Serializer\Tests\Fixtures\Node -> BDBStudios\Serializer\Tests\Fixtures\Node -> BDBStudios\Serializer\Tests\Fixtures\Node';
                }

                $self->assertEquals($expectedDepth, $context->getDepth(), 'shouldSkipProperty depth');
                $self->assertEquals($expectedPath, $context->getPath(), 'shouldSkipProperty path');

                return true;
            }))
            ->will($this->returnValue(false));

        $serializer = SerializerBuilder::create()->build();

        $serializer->serialize($object, 'json', SerializationContext::create()->addExclusionStrategy($exclusionStrategy));
    }

    public function testSerializationMetadataStack()
    {
        $object = new Node(array(
            $child = new InlineChild(),
        ));
        $self = $this;

        $exclusionStrategy = $this->getMock('BDBStudios\Serializer\Exclusion\ExclusionStrategyInterface');
        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipClass')
            ->will($this->returnCallback(function (ClassMetadata $classMetadata, SerializationContext $context) use ($self, $object, $child) {
                $stack = $context->getMetadataStack();

                if ($object === $context->getObject()) {
                    $self->assertEquals(0, $stack->count());
                }

                if ($child === $context->getObject()) {
                    $self->assertEquals(2, $stack->count());
                    $self->assertEquals('BDBStudios\Serializer\Tests\Fixtures\Node', $stack[1]->name);
                    $self->assertEquals('children', $stack[0]->name);
                }

                return false;
            }));

        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipProperty')
            ->will($this->returnCallback(function (PropertyMetadata $propertyMetadata, SerializationContext $context) use ($self, $object, $child) {
                $stack = $context->getMetadataStack();

                if ('BDBStudios\Serializer\Tests\Fixtures\Node' === $propertyMetadata->class && $propertyMetadata->name === 'children') {
                    $self->assertEquals(1, $stack->count());
                    $self->assertEquals('BDBStudios\Serializer\Tests\Fixtures\Node', $stack[0]->name);
                }

                if ('BDBStudios\Serializer\Tests\Fixtures\InlineChild' === $propertyMetadata->class) {
                    $self->assertEquals(3, $stack->count());
                    $self->assertEquals('BDBStudios\Serializer\Tests\Fixtures\Node', $stack[2]->name);
                    $self->assertEquals('children', $stack[1]->name);
                    $self->assertEquals('BDBStudios\Serializer\Tests\Fixtures\InlineChild', $stack[0]->name);
                }

                return false;
            }));

        $serializer = SerializerBuilder::create()->build();
        $serializer->serialize($object, 'json', SerializationContext::create()->addExclusionStrategy($exclusionStrategy));
    }
}
