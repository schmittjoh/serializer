<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Exclusion\DepthExclusionStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer as JMSSerializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class ArrayCollectionDepthTest extends TestCase
{
    /** @var JMSSerializer */
    private $serializer;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * @param array|Collection $collection
     *
     * @dataProvider getCollections
     */
    public function testDepth($collection)
    {
        $context = SerializationContext::create()
            ->addExclusionStrategy(new DepthExclusionStrategy());
        $result = $this->serializer->serialize(new CollectionWrapper($collection), 'json', $context);
        self::assertSame('{"collection":[{"name":"lvl1","next":{"name":"lvl2"}}]}', $result);
    }

    public static function getCollections()
    {
        $data = [new Node('lvl1', new Node('lvl2', new Node('lvl3')))];
        return [
            [$data],
            [new ArrayCollection($data)],
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
