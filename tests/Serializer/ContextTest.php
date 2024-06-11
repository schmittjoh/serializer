<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\BlogPost;
use JMS\Serializer\Tests\Fixtures\InlineChild;
use JMS\Serializer\Tests\Fixtures\Node;
use JMS\Serializer\Tests\Fixtures\Publisher;
use JMS\Serializer\Tests\Fixtures\VersionedObject;
use JMS\Serializer\VisitorInterface;
use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    public function testSerializationContextPathAndDepth()
    {
        $object = new Node([
            new Node(),
            new Node([new Node()]),
        ]);
        $objects = [$object, $object->children[0], $object->children[1], $object->children[1]->children[0]];

        $self = $this;

        $exclusionStrategy = $this->getMockBuilder(ExclusionStrategyInterface::class)->getMock();
        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipClass')
            ->with($this->anything(), $this->callback(static function (SerializationContext $context) use ($self, $objects) {
                $expectedDepth = $expectedPath = null;

                if ($context->getObject() === $objects[0]) {
                    $expectedDepth = 1;
                    $expectedPath = Node::class;
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
            ->willReturn(false);

        $exclusionStrategy->expects($this->any())
            ->method('shouldSkipProperty')
            ->with($this->anything(), $this->callback(static function (SerializationContext $context) use ($self, $objects) {
                $expectedDepth = $expectedPath = null;

                if ($context->getObject() === $objects[0]) {
                    $expectedDepth = 1;
                    $expectedPath = Node::class;
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
            ->willReturn(false);

        $serializer = SerializerBuilder::create()->build();

        $serializer->serialize($object, 'json', SerializationContext::create()->addExclusionStrategy($exclusionStrategy));
    }

    public function testSerializationMetadataStack()
    {
        $object = new Node([
            $child = new InlineChild(),
        ]);
        $self = $this;

        $exclusionStrategy = $this->getMockBuilder(ExclusionStrategyInterface::class)->getMock();
        $exclusionStrategy
            ->method('shouldSkipClass')
            ->willReturnCallback(static function (ClassMetadata $classMetadata, SerializationContext $context) use ($self, $object, $child) {
                $stack = $context->getMetadataStack();

                if ($object === $context->getObject()) {
                    $self->assertEquals(0, $stack->count());
                }

                if ($child === $context->getObject()) {
                    $self->assertEquals(2, $stack->count());
                    $self->assertEquals(Node::class, $stack[1]->name);
                    $self->assertEquals('children', $stack[0]->name);
                }

                return false;
            });

        $exclusionStrategy
            ->method('shouldSkipProperty')
            ->willReturnCallback(static function (PropertyMetadata $propertyMetadata, SerializationContext $context) use ($self) {
                $stack = $context->getMetadataStack();

                if ('JMS\Serializer\Tests\Fixtures\Node' === $propertyMetadata->class && 'children' === $propertyMetadata->name) {
                    $self->assertEquals(1, $stack->count());
                    $self->assertEquals(Node::class, $stack[0]->name);
                }

                if ('JMS\Serializer\Tests\Fixtures\InlineChild' === $propertyMetadata->class) {
                    $self->assertEquals(3, $stack->count());
                    $self->assertEquals(Node::class, $stack[2]->name);
                    $self->assertEquals('children', $stack[1]->name);
                    $self->assertEquals(InlineChild::class, $stack[0]->name);
                }

                return false;
            });

        $serializer = SerializerBuilder::create()->build();
        $serializer->serialize($object, 'json', SerializationContext::create()->addExclusionStrategy($exclusionStrategy));
    }

    public static function getScalars()
    {
        return [
            ['string'],
            [5],
            [5.5],
            [[]],
        ];
    }

    /**
     * @dataProvider getScalars
     */
    #[DataProvider('getScalars')]
    public function testCanVisitScalars($scalar)
    {
        $context = SerializationContext::create();

        $context->startVisiting($scalar);
        self::assertFalse($context->isVisiting($scalar));
        $context->stopVisiting($scalar);
    }

    public function testInitialTypeCompatibility()
    {
        $context = SerializationContext::create();
        $context->setInitialType('foo');
        self::assertEquals('foo', $context->getInitialType());
        self::assertEquals('foo', $context->getAttribute('initial_type'));

        $context = SerializationContext::create();
        $context->setAttribute('initial_type', 'foo');
        self::assertEquals('foo', $context->getInitialType());
    }

    public function testMultipleCallsOnGroupsDoNotCreateMultipleExclusionStrategies()
    {
        $serializer = SerializerBuilder::create()->build();

        $context = SerializationContext::create();
        $context->setGroups(['foo', 'Default']);
        $context->setGroups('post');

        $object = new BlogPost('serializer', new Author('me'), new \DateTime(), new Publisher('php'));
        $serialized = $serializer->serialize($object, 'json', $context);

        $data = json_decode($serialized, true);

        self::assertArrayHasKey('id', $data);
        self::assertArrayNotHasKey('created_at', $data);
    }

    public function testMultipleCallsOnVersionDoNotCreateMultipleExclusionStrategies()
    {
        $serializer = SerializerBuilder::create()->build();

        $context = SerializationContext::create();
        $context->setVersion('1.0.1');
        $context->setVersion('1.0.0');

        $object = new VersionedObject('a', 'b');
        $serialized = $serializer->serialize($object, 'json', $context);

        $data = json_decode($serialized, true);

        self::assertEquals('a', $data['name']);
    }

    public function testSerializeNullOption()
    {
        $context = SerializationContext::create();
        self::assertFalse($context->shouldSerializeNull());

        $context->setSerializeNull(false);
        self::assertFalse($context->shouldSerializeNull());

        $context->setSerializeNull(true);
        self::assertTrue($context->shouldSerializeNull());
    }

    public function testContextBecomesImmutableWhenStartingProcess()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('This context was already initialized and is immutable; you cannot modify it anymore.');

        $visitor = $this->createMock(VisitorInterface::class);
        $navigator = $this->createMock(GraphNavigator::class);
        $metadataFactory = $this->createMock(MetadataFactoryInterface::class);

        $context = SerializationContext::create();

        $context->initialize('json', $visitor, $navigator, $metadataFactory);

        $context->setSerializeNull(false);
    }
}
