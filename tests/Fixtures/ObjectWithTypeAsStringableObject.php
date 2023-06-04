<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithTypeAsStringableObject
{
    #[Serializer\Type(name: new StringableObjectType())]
    private $array;

    /**
     * @param array<string> $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }
}

class StringableObjectType
{
    public function __toString(): string
    {
        return 'array<string>';
    }
}
