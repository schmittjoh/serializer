<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

/** @Serializer\AccessorOrder("alphabetical") */
#[Serializer\AccessorOrder(order: 'alphabetical')]
class InlineParent
{
    /**
     * @Type("string")
     */
    #[Type(name: 'string')]
    private $c = 'c';

    /**
     * @Type("string")
     */
    #[Type(name: 'string')]
    private $d = 'd';

    /**
     * @Serializer\Inline
     *
     * @Type("JMS\Serializer\Tests\Fixtures\InlineChild")
     */
    #[Serializer\Inline]
    #[Type(name: 'JMS\Serializer\Tests\Fixtures\InlineChild')]
    private $child;

    public function __construct($child = null)
    {
        $this->child = $child ?: new InlineChild();
    }
}
