<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class Author implements \JsonSerializable
{
    /**
     * @Type("string")
     * @SerializedName("full_name")
     */
    #[Type(name: 'string')]
    #[SerializedName(name: 'full_name')]
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'json_full_name' => $this->name,
        ];
    }
}
