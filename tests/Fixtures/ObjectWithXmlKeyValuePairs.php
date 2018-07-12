<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlKeyValuePairs;

class ObjectWithXmlKeyValuePairs
{
    /**
     * @var array
     * @XmlKeyValuePairs
     */
    private $array = [
        'key-one' => 'foo',
        'key-two' => 1,
        'nested-array' => ['bar' => 'foo'],
        'without-keys' => [
            1,
            'test',
        ],
        'mixed' => [
            'test',
            'foo' => 'bar',
            '1_foo' => 'bar',
        ],
        1 => 'foo',
    ];
}
