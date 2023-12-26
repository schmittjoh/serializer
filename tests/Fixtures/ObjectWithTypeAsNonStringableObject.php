<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithTypeAsNonStringableObject
{
    #[Serializer\Type(new NonStringableObjectType())]
    private $array;

    #[Serializer\Type(name: new NonStringableObjectType())]
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

class NonStringableObjectType
{
}
