<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class CustomDeserializationObject
{
    /**
     * @Serializer\Type("string")
     */
    #[Serializer\Type(name: 'string')]
    public $someProperty;

    public function __construct($value)
    {
        $this->someProperty = $value;
    }
}
