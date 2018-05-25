<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

/** @Serializer\AccessorOrder("alphabetical") */
class InlineParent
{
    /**
     * @Type("string")
     */
    private $c = 'c';

    /**
     * @Type("string")
     */
    private $d = 'd';

    /**
     * @Serializer\Inline
     */
    private $child;

    public function __construct($child = null)
    {
        $this->child = $child ?: new InlineChild();
    }
}
