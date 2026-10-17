<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Deserializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\NonCastableTypeException;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Discriminator\Car;
use JMS\Serializer\Tests\Fixtures\GroupsObject;
use JMS\Serializer\Tests\Fixtures\Price;
use JMS\Serializer\Tests\Fixtures\Publisher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BaseDeserializationTest extends TestCase
{
    /**
     * @dataProvider dataTypeCannotBeCasted
     */
    #[DataProvider('dataTypeCannotBeCasted')]
    public function testDeserializationInvalidDataCausesException($data, string $type): void
    {
        $serializer = SerializerBuilder::create()->build();

        $this->expectException(NonCastableTypeException::class);

        $serializer->fromArray($data, $type);
    }

    public static function dataTypeCannotBeCasted(): iterable
    {
        yield 'array to string' => [
            ['pub_name' => ['bla', 'bla']],
            Publisher::class,
        ];

        yield 'object to float' => [
            ['price' => (object) ['bla' => 'bla']],
            Price::class,
        ];

        yield 'object to int' => [
            ['km' => (object) ['bla' => 'bla']],
            Car::class,
        ];

        yield 'float above int range to int' => [
            ['km' => 9223372036854775808.0],
            Car::class,
        ];

        yield 'float below int range to int' => [
            ['km' => -1.0e20],
            Car::class,
        ];

        yield 'infinite float to int' => [
            ['km' => INF],
            Car::class,
        ];
    }

    /**
     * @dataProvider dataFloatCanBeCastedToInt
     */
    #[DataProvider('dataFloatCanBeCastedToInt')]
    public function testDeserializationFloatInIntRange(float $data, int $expected): void
    {
        $serializer = SerializerBuilder::create()->build();

        $car = $serializer->fromArray(['km' => $data], Car::class);

        self::assertSame($expected, $car->km);
    }

    public static function dataFloatCanBeCastedToInt(): iterable
    {
        yield 'fraction' => [1.5, 1];
        yield 'lower bound' => [(float) PHP_INT_MIN, PHP_INT_MIN];
    }

    /**
     * @dataProvider dataDeserializerGroupExclusion
     */
    #[DataProvider('dataDeserializerGroupExclusion')]
    public function testDeserializerGroupExclusion(array $data, array $groups, array $expected): void
    {
        $serializer = SerializerBuilder::create()->build();
        $context = DeserializationContext::create()->setGroups($groups);
        $object = $serializer->fromArray($data, GroupsObject::class, $context);
        self::assertSame($expected, $serializer->toArray($object));
    }

    public static function dataDeserializerGroupExclusion(): iterable
    {
        $data = [
            'foo' => 'foo',
            'foobar' => 'foobar',
            'bar' => 'bar',
            'none' => 'none',
        ];

        yield [
            $data,
            ['Default'],
            [
                'bar' => 'bar',
                'none' => 'none',
            ],
        ];

        yield [
            $data,
            ['foo'],
            [
                'foo' => 'foo',
                'foobar' => 'foobar',
            ],
        ];

        yield [
            $data,
            ['bar'],
            [
                'foobar' => 'foobar',
                'bar' => 'bar',
            ],
        ];

        yield [
            $data,
            ['foo', 'bar'],
            [
                'foo' => 'foo',
                'foobar' => 'foobar',
                'bar' => 'bar',
            ],
        ];

        yield [
            $data,
            ['you_shall_not_pass'],
            [],
        ];
    }
}
