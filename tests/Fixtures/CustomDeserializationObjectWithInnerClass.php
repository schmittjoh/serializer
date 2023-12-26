<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class CustomDeserializationObjectWithInnerClass
{
    /**
     * @Serializer\Type("JMS\Serializer\Tests\Fixtures\CustomDeserializationObject")
     */
    #[Serializer\Type(name: CustomDeserializationObject::class)]
    private $someProperty;

    public function __construct(CustomDeserializationObject $value)
    {
        $this->someProperty = $value;
    }
}
