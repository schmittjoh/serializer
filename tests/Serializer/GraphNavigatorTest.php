<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Accessor\DefaultAccessorStrategy;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\GraphNavigator\DeserializationGraphNavigator;
use JMS\Serializer\GraphNavigator\SerializationGraphNavigator;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Metadata\MetadataFactory;
use PHPUnit\Framework\TestCase;

class GraphNavigatorTest extends TestCase
{
    private $metadataFactory;
    private $handlerRegistry;
    private $objectConstructor;
    private $dispatcher;
    private $serializationNavigator;
    private $deserializationNavigator;
    private $context;
    private $deserializationContext;
    private $accessor;

    private $serializationVisitor;
    private $deserializationVisitor;

    /**
     * @expectedException JMS\Serializer\Exception\RuntimeException
     * @expectedExceptionMessage Resources are not supported in serialized data.
     */
    public function testResourceThrowsException()
    {
        $this->serializationNavigator->accept(STDIN, null);
    }

    public function testNavigatorPassesInstanceOnSerialization()
    {
        $object = new SerializableClass();
        $metadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        $self = $this;
        $this->context = $this->getMockBuilder(SerializationContext::class)->getMock();
        $context = $this->context;
        $exclusionStrategy = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock();
        $exclusionStrategy->expects($this->once())
            ->method('shouldSkipClass')
            ->will($this->returnCallback(static function ($passedMetadata, $passedContext) use ($metadata, $context, $self) {
                $self->assertSame($metadata, $passedMetadata);
                $self->assertSame($context, $passedContext);
                return false;
            }));
        $exclusionStrategy->expects($this->once())
            ->method('shouldSkipProperty')
            ->will($this->returnCallback(static function ($propertyMetadata, $passedContext) use ($context, $metadata, $self) {
                $self->assertSame($metadata->propertyMetadata['foo'], $propertyMetadata);
                $self->assertSame($context, $passedContext);
                return false;
            }));

        $this->context->expects($this->once())
            ->method('getExclusionStrategy')
            ->will($this->returnValue($exclusionStrategy));

        $navigator = new SerializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->accessor, $this->dispatcher);
        $navigator->initialize($this->serializationVisitor, $this->context);
        $navigator->accept($object, null);
    }

    public function testNavigatorPassesNullOnDeserialization()
    {
        $class = __NAMESPACE__ . '\SerializableClass';
        $metadata = $this->metadataFactory->getMetadataForClass($class);

        $this->context = $this->getMockBuilder(DeserializationContext::class)->getMock();

        $context = $this->context;
        $exclusionStrategy = $this->getMockBuilder(ExclusionStrategyInterface::class)->getMock();
        $exclusionStrategy->expects($this->once())
            ->method('shouldSkipClass')
            ->with($metadata, $this->callback(static function ($navigatorContext) use ($context) {
                return $navigatorContext === $context;
            }));

        $exclusionStrategy->expects($this->once())
            ->method('shouldSkipProperty')
            ->with($metadata->propertyMetadata['foo'], $this->callback(static function ($navigatorContext) use ($context) {
                return $navigatorContext === $context;
            }));

        $this->context->expects($this->once())
            ->method('getExclusionStrategy')
            ->will($this->returnValue($exclusionStrategy));

        $navigator = new DeserializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->objectConstructor, $this->accessor, $this->dispatcher);
        $navigator->initialize($this->deserializationVisitor, $this->context);
        $navigator->accept('random', ['name' => $class, 'params' => []]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testNavigatorChangeTypeOnSerialization()
    {
        $object = new SerializableClass();
        $typeName = 'JsonSerializable';

        $this->dispatcher->addListener('serializer.pre_serialize', static function ($event) use ($typeName) {
            $type = $event->getType();
            $type['name'] = $typeName;
            $event->setType($type['name'], $type['params']);
        });

        $this->handlerRegistry->registerSubscribingHandler(new TestSubscribingHandler());

        $navigator = new SerializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->accessor, $this->dispatcher);
        $navigator->initialize($this->serializationVisitor, $this->context);
        $navigator->accept($object, null);
    }

    protected function setUp()
    {
        $this->deserializationVisitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $this->serializationVisitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();

        $this->context = $this->getMockBuilder(SerializationContext::class)
            ->enableOriginalConstructor()
            ->setMethodsExcept(['getExclusionStrategy'])
            ->getMock();

        $this->deserializationContext = $this->getMockBuilder(DeserializationContext::class)
            ->enableOriginalConstructor()
            ->setMethodsExcept(['getExclusionStrategy'])
            ->getMock();

        $this->dispatcher = new EventDispatcher();
        $this->accessor = new DefaultAccessorStrategy();
        $this->handlerRegistry = new HandlerRegistry();
        $this->objectConstructor = new UnserializeObjectConstructor();

        $this->metadataFactory = new MetadataFactory(new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy()));

        $this->serializationNavigator = new SerializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->accessor, $this->dispatcher);
        $this->serializationNavigator->initialize($this->serializationVisitor, $this->context);

        $this->deserializationNavigator = new DeserializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->objectConstructor, $this->accessor, $this->dispatcher);
        $this->deserializationNavigator->initialize($this->deserializationVisitor, $this->deserializationContext);
    }
}

class SerializableClass
{
    public $foo = 'bar';
}

class TestSubscribingHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [[
            'type' => 'JsonSerializable',
            'format' => 'foo',
            'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            'method' => 'serialize',
        ],
        ];
    }
}
