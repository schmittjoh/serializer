<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithTypeAsNonStringableObject
{
    #[Serializer\Type(name: new NonStringableObjectType())]
    private $array;

    /**
     * @param array<string> $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }
}

class NonStringableObjectType
{
}
