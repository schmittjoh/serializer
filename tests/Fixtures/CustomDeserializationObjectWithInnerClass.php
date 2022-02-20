<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class CustomDeserializationObjectWithInnerClass
{
    /**
     * @Type("JMS\Serializer\Tests\Fixtures\CustomDeserializationObject")
     */
    #[Type(name: CustomDeserializationObject::class)]
    private $someProperty;

    public function __construct(CustomDeserializationObject $value)
    {
        $this->someProperty = $value;
    }
}
