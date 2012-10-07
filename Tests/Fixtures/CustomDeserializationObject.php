<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\Type;

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
