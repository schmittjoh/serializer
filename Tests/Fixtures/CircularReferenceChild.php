<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

class CircularReferenceChild
{
    private $name;

    /**
     * @Exclude
     */
    private $parent;

    public function __construct($name, CircularReferenceParent $parent)
    {
        $this->name   = $name;
        $this->parent = $parent;
    }
}