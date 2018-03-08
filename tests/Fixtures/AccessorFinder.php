<?php

namespace JMS\Serializer\Tests\Fixtures;
use JMS\Serializer\Annotation as Serializer;

/** @Serializer\AccessorFinder() */
final class AccessorFinder
{
    /**
     * @Serializer\Type("integer")
     */
    private $a1_b = 1;

    public function setA1B($value)
    {
        $this->a1_b = $value;
    }

    public function getA1B()
    {
        return $this->a1_b;
    }
}
