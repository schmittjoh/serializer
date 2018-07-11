<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlKeyValuePairs;

class ObjectWithXmlKeyValuePairsWithType
{
    /**
     * @var array
     * @Type("array<string,string>")
     * @XmlKeyValuePairs
     */
    private $list;

    /**
     * @var array
     * @Type("array<string>")
     */
    private $list2;

    public function __construct(array $list, array $list2 = [])
    {
        $this->list = $list;
        $this->list2 = $list2;
    }

    public static function create1()
    {
        return new self(
            [
                'key-one' => 'foo',
                'key-two' => 'bar',
            ]
        );
    }

    public static function create2()
    {
        return new self(
            [
                'key_01' => 'One',
                'key_02' => 'Two',
                'key_03' => 'Three',
            ],
            ['Four']
        );
    }
}
