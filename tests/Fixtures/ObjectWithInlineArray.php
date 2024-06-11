<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

final class ObjectWithInlineArray
{
    /**
     * @Serializer\Inline()
     * @Serializer\Type("array<string,string>")
     */
    #[Serializer\Inline]
    #[Serializer\Type(name: 'array<string,string>')]
    public $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }
}
