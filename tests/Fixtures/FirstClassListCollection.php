<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class FirstClassListCollection
{
    /**
     * @Serializer\Type("array<int>")
     * @Serializer\Inline
     *
     * @var int[]
     */
    public $items = [];

    public function __construct(array $items)
    {
        $this->items = $items;
    }
}
