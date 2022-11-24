<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Accessor\DefaultAccessorStrategy;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\SkipHandlerException;
use JMS\Serializer\Exclusion\DisjunctExclusionStrategy;
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
use JMS\Serializer\VisitorInterface;
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

    public function testResourceThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Resources are not supported in serialized data.');

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
            ->will($this->returnValue(new DisjunctExclusionStrategy([$exclusionStrategy])));

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
            ->will($this->returnValue(new DisjunctExclusionStrategy([$exclusionStrategy])));

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
        $this->context->initialize(TestSubscribingHandler::FORMAT, $this->serializationVisitor, $navigator, $this->metadataFactory);

        $navigator->accept($object, null);
    }

    public function testExposeAcceptHandlerExceptionOnSerialization()
    {
        $object = new SerializableClass();
        $typeName = 'JsonSerializable';
        $msg = 'Useful serialization error with relevant context information';

        $handler = static function ($visitor, $data, array $type, SerializationContext $context) use ($msg) {
            $context->startVisiting(new \stdClass());

            throw new \RuntimeException($msg);
        };
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, $typeName, TestSubscribingHandler::FORMAT, $handler);

        $this->context->method('getFormat')->willReturn(TestSubscribingHandler::FORMAT);

        $navigator = new SerializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->accessor, $this->dispatcher);
        $navigator->initialize($this->serializationVisitor, $this->context);
        $this->context->initialize(TestSubscribingHandler::FORMAT, $this->serializationVisitor, $navigator, $this->metadataFactory);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($msg);
        $navigator->accept($object, ['name' => $typeName, 'params' => []]);
    }

    public function testHandlerIsExecutedOnSerialization()
    {
        $object = new SerializableClass();
        $this->handlerRegistry->registerSubscribingHandler(new TestSubscribingHandler());

        $this->context->method('getFormat')->willReturn(TestSubscribingHandler::FORMAT);

        $navigator = new SerializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->accessor, $this->dispatcher);
        $navigator->initialize($this->serializationVisitor, $this->context);
        $this->context->initialize(TestSubscribingHandler::FORMAT, $this->serializationVisitor, $navigator, $this->metadataFactory);

        $rt = $navigator->accept($object, null);
        $this->assertEquals('foobar', $rt);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFilterableHandlerIsSkippedOnSerialization()
    {
        $object = new SerializableClass();
        $this->handlerRegistry->registerSubscribingHandler(new TestSkippableSubscribingHandler());

        $this->context->method('getFormat')->willReturn(TestSkippableSubscribingHandler::FORMAT);

        $navigator = new SerializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->accessor, $this->dispatcher);
        $navigator->initialize($this->serializationVisitor, $this->context);
        $this->context->initialize(TestSkippableSubscribingHandler::FORMAT, $this->serializationVisitor, $navigator, $this->metadataFactory);

        $navigator->accept($object, null);
    }

    public function testFilterableHandlerIsNotSkippedOnSerialization()
    {
        $object = new SerializableClass();
        $this->handlerRegistry->registerSubscribingHandler(new TestSkippableSubscribingHandler(false));

        $this->context->method('getFormat')->willReturn(TestSkippableSubscribingHandler::FORMAT);

        $navigator = new SerializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->accessor, $this->dispatcher);
        $navigator->initialize($this->serializationVisitor, $this->context);
        $this->context->initialize(TestSkippableSubscribingHandler::FORMAT, $this->serializationVisitor, $navigator, $this->metadataFactory);

        $this->expectException(NotAcceptableException::class);
        $this->expectExceptionMessage(TestSkippableSubscribingHandler::EX_MSG);
        $navigator->accept($object, null);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testNavigatorDoesNotCrashWhenObjectConstructorReturnsNull()
    {
        $objectConstructor = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $objectConstructor->method('construct')->willReturn(null);
        $navigator = new DeserializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $objectConstructor, $this->accessor, $this->dispatcher);
        $navigator->initialize($this->deserializationVisitor, $this->deserializationContext);

        $navigator->accept(['id' => 1234], ['name' => SerializableClass::class]);
    }

    protected function setUp(): void
    {
        $this->deserializationVisitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $this->serializationVisitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();

        $this->context = $this->getMockBuilder(SerializationContext::class)
            ->enableOriginalConstructor()
            ->setMethodsExcept(['getExclusionStrategy', 'initialize', 'startVisiting', 'stopVisiting'])
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
    public const FORMAT = 'foo';

    public static function getSubscribingMethods()
    {
        return [
            [
                'type' => SerializableClass::class,
                'format' => self::FORMAT,
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serialize',
            ],
        ];
    }

    public function serialize(VisitorInterface $visitor, $userData, array $type, Context $context)
    {
        return 'foobar';
    }
}

class TestSkippableSubscribingHandler implements SubscribingHandlerInterface
{
    public const FORMAT = 'foo';
    public const EX_MSG = 'This method should be skipped!';

    private $shouldSkip;

    public function __construct(bool $shouldSkip = true)
    {
        $this->shouldSkip = $shouldSkip;
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'type' => SerializableClass::class,
                'format' => self::FORMAT,
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serialize',
            ],
        ];
    }

    public function serialize(VisitorInterface $visitor, $userData, array $type, Context $context)
    {
        if ($this->shouldSkip) {
            throw new SkipHandlerException();
        }

        throw new NotAcceptableException(self::EX_MSG);
    }
}
