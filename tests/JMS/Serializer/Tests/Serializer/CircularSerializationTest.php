<?php

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

/**
 * Class CircularSerializationTest
 *
 * @author Juan de Dios Dubé Herdugo <juandedios.dube@gmail.com>
 */
class CircularSerializationTest extends \PHPUnit_Framework_TestCase
{
    public function testCircularJsonSerialization()
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
            $dispatcher->addListener(Events::CIRCULAR_SERIALIZATION, function($event) use ($resultExpected) {
                $event->setReplacement($resultExpected);
            });
        });
        $serializer = $builder->build();

        $data = $serializer->serialize($parent, 'json');
        $result = json_decode($data, true);
        $this->assertEquals($result['child']['parent'], $resultExpected);

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
