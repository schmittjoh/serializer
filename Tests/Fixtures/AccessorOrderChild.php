<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation as Serializer;

/** @Serializer\AccessorOrder("custom", custom = {"c", "d", "a", "b"}) */
class AccessorOrderChild extends AccessorOrderParent
{
    private $c = 'c', $d = 'd';
}