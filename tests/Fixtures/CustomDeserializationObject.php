<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class CustomDeserializationObject
{
    /**
     * @Type("string")
     */
    public $someProperty;

    public function __construct($value)
    {
        $this->someProperty = $value;
    }
}
