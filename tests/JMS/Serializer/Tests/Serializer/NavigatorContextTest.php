<?php

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\GraphNavigator;
use Metadata\MetadataFactory;
use JMS\Serializer\Tests\Fixtures\Node;
use JMS\Serializer\JsonSerializationVisitor;
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
        $serializer->setExclusionStrategy($exclusionStrategy);

        $serializer->serialize($object, 'json');
    }
}
