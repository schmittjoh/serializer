<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithTypeAsStringableObject
{
    #[Serializer\Type(new StringableObjectType())]
    private $array;

    #[Serializer\Type(name: new StringableObjectType())]
    private $array2;

    /**
     * @param array<string> $array
     * @param array<string> $array2
     */
    public function __construct(array $array, array $array2)
    {
        $this->array = $array;
        $this->array2 = $array2;
    }
}

class StringableObjectType
{
    public function __toString(): string
    {
        return 'array<string>';
    }
}
