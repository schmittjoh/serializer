<?php

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\EventDispatcher\CircularSerializationEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\SerializerBuilder;
use Metadata\MetadataFactory;

/**
 * Class CircularSerializationTest
 *
 * @author Juan de Dios Dubé Herdugo <juandedios.dube@gmail.com>
 */
class CircularSerializationTest extends \PHPUnit_Framework_TestCase
{
    private $metadataFactory;
    private $handlerRegistry;
    private $objectConstructor;
    private $dispatcher;
    private $navigator;
    private $context;

    public function testNavigatorCircularSerialization()
    {
        $resultExpected = 'This is my parent';
        $parent = new ParentClass();
        $child  = new ChildClass();
        $parent->child = $child;
        $child->parent = $parent;

        /** @var SerializerInterface $serializer */
        $builder = SerializerBuilder::create();
        $builder->configureListeners(function($dispatcher) use ($resultExpected) {
            /** @var EventDispatcher $dispatcher */
            $dispatcher->addListener('serializer.circular_serialization', function($event) use ($resultExpected) {
                $event->setReplacement($resultExpected);
            });
        });
        $serializer = $builder->build();

        $data = $serializer->serialize($parent, 'json');
        $result = json_decode($data, true);
        $this->assertEquals($result['child']['parent'], $resultExpected);

    }

    protected function setUp()
    {
        $this->context = $this->getMock('JMS\Serializer\Context');
        $this->dispatcher = new EventDispatcher();
        $this->handlerRegistry = new HandlerRegistry();
        $this->objectConstructor = new UnserializeObjectConstructor();
        $this->metadataFactory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $this->navigator = new GraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->objectConstructor, $this->dispatcher);
    }
}

class ParentClass
{
    public $child = null;
}

class ChildClass
{
    public $parent = null;
}

class TestCircularSerializationListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.circular_serialization', 'method' => 'onCircularSerialization'],
        ];
    }

    public function onCircularSerialization(CircularSerializationEvent $event)
    {
        $event->setReplacement('This is my parent');
    }
}
