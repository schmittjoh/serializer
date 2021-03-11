<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Ordering;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Ordering\CustomPropertyOrderingStrategy;
use PHPUnit\Framework\TestCase;

class CustomPropertyOrderingStrategyTest extends TestCase
{
    /**
     * @dataProvider dataOrder
     */
    public function testOrder(array $ordering, array $keysToSort, array $expectedResult): void
    {
        $strategy = new CustomPropertyOrderingStrategy(array_flip($ordering));

        $properties = array_combine(
            $keysToSort,
            array_pad([], count($keysToSort), $this->createMock(PropertyMetadata::class))
        );
        $sortedProperties = $strategy->order($properties);
        self::assertEquals($expectedResult, array_keys($sortedProperties));
    }

    public function dataOrder(): iterable
    {
        $order = ['one', 'two', 'three'];

        yield [
            $order,
            ['three', 'two', 'one'],
            $order,
        ];

        $order = ['g', 'a', 'm', 'b', 'i', 't', 'k', 'o'];

        yield [
            $order,
            ['k', 'a', 'm', 'b', 'o', 'g', 'i', 't'],
            $order,
        ];

        yield [
            ['a', 'c', 'e', 'g', 'i', 'k', 'm', 't'],
            ['f', 'e', 'd', 'c', 'b', 'a'],
            ['a', 'c', 'e', 'f', 'd', 'b'],
        ];
    }
}
