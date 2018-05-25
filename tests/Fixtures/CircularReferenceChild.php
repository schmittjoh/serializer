<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class CircularReferenceChild
{
    /** @Type("string") */
    private $name;

    /** @Type("JMS\Serializer\Tests\Fixtures\CircularReferenceParent") */
    private $parent;

    public function __construct($name, CircularReferenceParent $parent)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(CircularReferenceParent $parent)
    {
        $this->parent = $parent;
    }
}
