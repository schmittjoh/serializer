<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation as Serializer;
use JMS\SerializerBundle\Annotation\Type;

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
     * @Type("JMS\SerializerBundle\Tests\Fixtures\InlineChild")
     * @Serializer\Inline
     */
    private $child;

    public function __construct()
    {
        $this->child = new InlineChild();
    }
}