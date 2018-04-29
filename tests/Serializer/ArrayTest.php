<?php

declare(strict_types=1);

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\AuthorList;
use JMS\Serializer\Tests\Fixtures\Order;
use JMS\Serializer\Tests\Fixtures\Price;

class ArrayTest extends \PHPUnit\Framework\TestCase
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
            'cost' => [
                'price' => 5
            ]
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
            'cost' => [
                'price' => 2.5
            ]
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
