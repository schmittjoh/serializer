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
     * @Type("JMS\Serializer\Tests\Fixtures\InlineChild")
     * @Serializer\Inline
     */
    private $child;

    public function __construct()
    {
        $this->child = new InlineChild();
    }
}