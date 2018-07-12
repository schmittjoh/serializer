<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\AuthorList;
use JMS\Serializer\Tests\Fixtures\Order;
use JMS\Serializer\Tests\Fixtures\Price;
use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase
{
    protected $serializer;

    public function setUp()
    {
        $builder = SerializerBuilder::create();
        $this->serializer = $builder->build();
    }

    public function testToArray()
    {
        $order = new Order(new Price(5));

        $expected = [
            'cost' => ['price' => 5],
        ];

        $result = $this->serializer->toArray($order);

        self::assertEquals($expected, $result);
    }

    /**
     * @dataProvider scalarValues
     */
    public function testToArrayWithScalar($input)
    {
        $this->expectException('JMS\Serializer\Exception\RuntimeException');
        $this->expectExceptionMessage(sprintf(
            'The input data of type "%s" did not convert to an array, but got a result of type "%s".',
            gettype($input),
            gettype($input)
        ));
        $result = $this->serializer->toArray($input);

        self::assertEquals([$input], $result);
    }

    public function scalarValues()
    {
        return [
            [42],
            [3.14159],
            ['helloworld'],
            [true],
        ];
    }

    public function testFromArray()
    {
        $data = [
            'cost' => ['price' => 2.5],
        ];

        $expected = new Order(new Price(2.5));
        $result = $this->serializer->fromArray($data, 'JMS\Serializer\Tests\Fixtures\Order');

        self::assertEquals($expected, $result);
    }

    public function testToArrayReturnsArrayObjectAsArray()
    {
        $result = $this->serializer->toArray(new Author(null));

        self::assertSame([], $result);
    }

    public function testToArrayConversNestedArrayObjects()
    {
        $list = new AuthorList();
        $list->add(new Author(null));

        $result = $this->serializer->toArray($list);
        self::assertSame(['authors' => [[]]], $result);
    }
}
