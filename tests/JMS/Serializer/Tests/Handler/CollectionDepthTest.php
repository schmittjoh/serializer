<?php

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Exclusion\DepthExclusionStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

class CollectionDepthTest extends \PHPUnit_Framework_TestCase
{

    /** @var \JMS\Serializer\Serializer */
    private $serializer;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * @dataProvider getCollections
     */
    public function testDepth($collection)
    {
        $context = SerializationContext::create()
            ->addExclusionStrategy(new DepthExclusionStrategy());
        $result = $this->serializer->serialize(new CollectionWrapper($collection), 'json', $context);
        $this->assertSame('{"collection":[{"name":"lvl1","next":{"name":"lvl2"}}]}', $result);
    }

    public static function getCollections()
    {
        $data = [new Node('lvl1', new Node('lvl2', new Node('lvl3')))];
        return [
            [$data],
            [new \Doctrine\Common\Collections\ArrayCollection($data)],
            [new \PropelObjectCollection($data)],
            [new \PhpCollection\Sequence($data)],
            [new \PhpCollection\Map($data)],
        ];
    }
}

class CollectionWrapper
{

    /**
     * @Serializer\MaxDepth(2)
     */
    public $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }
}

class Node
{

    public $name;

    public $next;

    public function __construct($name, $next = null)
    {
        $this->name = $name;
        $this->next = $next;
    }
}
