<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class FirstClassMapCollection
{
    /**
     * @Serializer\Type("array<string,string>")
     * @Serializer\Inline
     *
     * @var array<string, string>
     */
    #[Serializer\Type(name: 'array<string,string>')]
    #[Serializer\Inline]
    public $items = [];

    public function __construct(array $items)
    {
        $this->items = $items;
    }
}
