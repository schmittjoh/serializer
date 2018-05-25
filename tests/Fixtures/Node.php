<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class Node
{
    /**
     * @Serializer\MaxDepth(2)
     */
    public $children;

    public $foo = 'bar';

    public function __construct($children = array())
    {
        $this->children = $children;
    }
}
