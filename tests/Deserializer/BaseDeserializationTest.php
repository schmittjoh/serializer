<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Deserializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\GroupsObject;
use PHPUnit\Framework\TestCase;

class BaseDeserializationTest extends TestCase
{
    /**
     * @dataProvider dataDeserializerGroupExclusion
     */
    public function testDeserializerGroupExclusion(array $data, array $groups, array $expected): void
    {
        $serializer = SerializerBuilder::create()->build();
        $context = DeserializationContext::create()->setGroups($groups);
        $object = $serializer->fromArray($data, GroupsObject::class, $context);
        self::assertSame($expected, $serializer->toArray($object));
    }

    public function dataDeserializerGroupExclusion(): iterable
    {
        $data = [
            'foo'    => 'foo',
            'foobar' => 'foobar',
            'bar'    => 'bar',
            'none'   => 'none',
        ];

        yield [
            $data,
            ['Default'],
            [
                'bar'  => 'bar',
                'none' => 'none',
            ],
        ];

        yield [
            $data,
            ['foo'],
            [
                'foo'    => 'foo',
                'foobar' => 'foobar',
            ],
        ];

        yield [
            $data,
            ['bar'],
            [
                'foobar' => 'foobar',
                'bar'    => 'bar',
            ],
        ];

        yield [
            $data,
            ['foo', 'bar'],
            [
                'foo'    => 'foo',
                'foobar' => 'foobar',
                'bar'    => 'bar',
            ],
        ];

        yield [
            $data,
            ['you_shall_not_pass'],
            [],
        ];
    }
}
