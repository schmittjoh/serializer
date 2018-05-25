<?php

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\InlineChild;
use JMS\Serializer\Tests\Fixtures\Node;

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

        $exclusionStrategy = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock();
        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipClass')
            ->with($this->anything(), $this->callback(function (SerializationContext $context) use ($self, $objects) {
                $expectedDepth = $expectedPath = null;

                if ($context->getObject() === $objects[0]) {
                    $expectedDepth = 1;
                    $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[1]) {
                    $expectedDepth = 2;
                    $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[2]) {
                    $expectedDepth = 2;
                    $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[3]) {
                    $expectedDepth = 3;
                    $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
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
                    $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[1]) {
                    $expectedDepth = 2;
                    $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[2]) {
                    $expectedDepth = 2;
                    $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
                } elseif ($context->getObject() === $objects[3]) {
                    $expectedDepth = 3;
                    $expectedPath = 'JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node -> JMS\Serializer\Tests\Fixtures\Node';
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

        $exclusionStrategy = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock();
        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipClass')
            ->will($this->returnCallback(function (ClassMetadata $classMetadata, SerializationContext $context) use ($self, $object, $child) {
                $stack = $context->getMetadataStack();

                if ($object === $context->getObject()) {
                    $self->assertEquals(0, $stack->count());
                }

                if ($child === $context->getObject()) {
                    $self->assertEquals(2, $stack->count());
                    $self->assertEquals('JMS\Serializer\Tests\Fixtures\Node', $stack[1]->name);
                    $self->assertEquals('children', $stack[0]->name);
                }

                return false;
            }));

        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipProperty')
            ->will($this->returnCallback(function (PropertyMetadata $propertyMetadata, SerializationContext $context) use ($self, $object, $child) {
                $stack = $context->getMetadataStack();

                if ('JMS\Serializer\Tests\Fixtures\Node' === $propertyMetadata->class && $propertyMetadata->name === 'children') {
                    $self->assertEquals(1, $stack->count());
                    $self->assertEquals('JMS\Serializer\Tests\Fixtures\Node', $stack[0]->name);
                }

                if ('JMS\Serializer\Tests\Fixtures\InlineChild' === $propertyMetadata->class) {
                    $self->assertEquals(3, $stack->count());
                    $self->assertEquals('JMS\Serializer\Tests\Fixtures\Node', $stack[2]->name);
                    $self->assertEquals('children', $stack[1]->name);
                    $self->assertEquals('JMS\Serializer\Tests\Fixtures\InlineChild', $stack[0]->name);
                }

                return false;
            }));

        $serializer = SerializerBuilder::create()->build();
        $serializer->serialize($object, 'json', SerializationContext::create()->addExclusionStrategy($exclusionStrategy));
    }

    public function getScalars()
    {
        return array(
            array("string"),
            array(5),
            array(5.5),
            array(array())
        );
    }

    /**
     * @dataProvider getScalars
     */
    public function testCanVisitScalars($scalar)
    {
        $context = SerializationContext::create();

        $context->startVisiting($scalar);
        $this->assertFalse($context->isVisiting($scalar));
        $context->stopVisiting($scalar);
    }

    public function testInitialTypeCompatibility()
    {
        $context = SerializationContext::create();
        $context->setInitialType('foo');
        $this->assertEquals('foo', $context->getInitialType());
        $this->assertEquals('foo', $context->attributes->get('initial_type')->get());

        $context = SerializationContext::create();
        $context->attributes->set('initial_type', 'foo');
        $this->assertEquals('foo', $context->getInitialType());
    }

    public function testSerializeNullOption()
    {
        $context = SerializationContext::create();
        $this->assertNull($context->shouldSerializeNull());

        $context->setSerializeNull(false);
        $this->assertFalse($context->shouldSerializeNull());

        $context->setSerializeNull(true);
        $this->assertTrue($context->shouldSerializeNull());

        $context->setSerializeNull("foo");
        $this->assertTrue($context->shouldSerializeNull());

        $context->setSerializeNull("0");
        $this->assertFalse($context->shouldSerializeNull());
    }
}
