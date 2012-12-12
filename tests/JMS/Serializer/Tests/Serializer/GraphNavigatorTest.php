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

class GraphNavigatorTest extends \PHPUnit_Framework_TestCase
{
    private $metadataFactory;
    private $handlerRegistry;
    private $objectConstructor;
    private $exclusionStrategy;
    private $dispatcher;
    private $navigator;
    private $visitor;

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Resources are not supported in serialized data.
     */
    public function testResourceThrowsException()
    {
        $this->navigator->accept(STDIN, null, $this->visitor);
    }

    public function testNavigatorPassesInstanceOnSerialization()
    {
        $object = new SerializableClass;
        $metadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        $exclusionStrategy = $this->getMock('JMS\Serializer\Exclusion\ExclusionStrategyInterface');
        $exclusionStrategy->expects($this->once())
            ->method('shouldSkipClass')
            ->with($metadata, $this->callback(function ($navigatorContext) use ($object) {
                return
                    $object === $navigatorContext->getObject()
                    && $navigatorContext->isSerializing()
                    && 'foo' === $navigatorContext->getFormat()
                ;
            }));
        $exclusionStrategy->expects($this->once())
            ->method('shouldSkipProperty')
            ->with($metadata->propertyMetadata['foo'], $this->callback(function ($navigatorContext) use ($object) {
                return
                    $object === $navigatorContext->getObject()
                    && $navigatorContext->isSerializing()
                    && 'foo' === $navigatorContext->getFormat()
                ;
            }));

        $this->navigator = new GraphNavigator(GraphNavigator::DIRECTION_SERIALIZATION, $this->metadataFactory, 'foo', $this->handlerRegistry, $this->objectConstructor, $exclusionStrategy, $this->dispatcher);
        $this->navigator->accept($object, null, $this->visitor);
    }

    public function testNavigatorPassesNullOnDeserialization()
    {
        $class = __NAMESPACE__.'\SerializableClass';
        $metadata = $this->metadataFactory->getMetadataForClass($class);

        $exclusionStrategy = $this->getMock('JMS\Serializer\Exclusion\ExclusionStrategyInterface');
        $exclusionStrategy->expects($this->once())
            ->method('shouldSkipClass')
            ->with($metadata, $this->callback(function ($navigatorContext) {
                return
                    $navigatorContext->getObject() === null
                    && !$navigatorContext->isSerializing()
                    && 'foo' === $navigatorContext->getFormat()
                ;
            }));

        $exclusionStrategy->expects($this->once())
            ->method('shouldSkipProperty')
            ->with($metadata->propertyMetadata['foo'], $this->callback(function ($navigatorContext) {
                return
                    $navigatorContext->getObject() === null
                    && !$navigatorContext->isSerializing()
                    && 'foo' === $navigatorContext->getFormat()
                ;
            }));

        $this->navigator = new GraphNavigator(GraphNavigator::DIRECTION_DESERIALIZATION, $this->metadataFactory, 'foo', $this->handlerRegistry, $this->objectConstructor, $exclusionStrategy, $this->dispatcher);
        $this->navigator->accept('random', array('name' => $class, 'params' => array()), $this->visitor);
    }

    public function testNavigatorChangeTypeOnSerialization()
    {
        $object = new SerializableClass;
        $typeName = 'JsonSerializable';

        $this->dispatcher->addListener('serializer.pre_serialize', function($event) use ($typeName) {
            $type = $event->getType();
            $type['name'] = $typeName;
            $event->setType($type['name'], $type['params']);
        });

        $subscribingHandlerClass = $this->getMockClass('JMS\Serializer\Handler\SubscribingHandlerInterface', array('getSubscribingMethods', 'serialize'));
        $subscribingHandlerClass::staticExpects($this->once())
            ->method('getSubscribingMethods')
            ->will($this->returnValue(array(array(
                'type' => $typeName,
                'format' => 'foo',
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'method' => 'serialize'
            ))));

        $subscribingHandler = new $subscribingHandlerClass();
        $subscribingHandler->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($this->visitor), $this->equalTo($object));

        $this->handlerRegistry->registerSubscribingHandler($subscribingHandler);

        $this->navigator = new GraphNavigator(GraphNavigator::DIRECTION_SERIALIZATION, $this->metadataFactory, 'foo', $this->handlerRegistry, $this->objectConstructor, null, $this->dispatcher);
        $this->navigator->accept($object, null, $this->visitor);
    }

    protected function setUp()
    {
        $this->visitor = $this->getMock('JMS\Serializer\VisitorInterface');
        $this->dispatcher = new EventDispatcher();
        $this->handlerRegistry = new HandlerRegistry();
        $this->objectConstructor = new UnserializeObjectConstructor();

        $this->metadataFactory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $this->navigator = new GraphNavigator(GraphNavigator::DIRECTION_SERIALIZATION, $this->metadataFactory, 'foo', $this->handlerRegistry, $this->objectConstructor, null, $this->dispatcher);
    }
}

class SerializableClass
{
    public $foo = 'bar';
}
