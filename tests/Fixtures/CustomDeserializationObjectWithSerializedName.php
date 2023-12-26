<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class CustomDeserializationObjectWithSerializedName
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     */
    #[Serializer\Type(name: 'string')]
    #[Serializer\SerializedName(name: 'name')]
    public $someProperty;

    public function __construct($value)
    {
        $this->someProperty = $value;
    }
}
