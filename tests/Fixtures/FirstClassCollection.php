<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class FirstClassCollection implements \IteratorAggregate
{
    /**
     * @Serializer\Type("array<int>")
     * @Serializer\Inline
     * @var int[]
     */
    public $items = [];

    public function __construct(int ...$items)
    {
        $this->items = $items;
    }

    public function getIterator() : iterable
    {
        yield from $this->items;
    }
}
