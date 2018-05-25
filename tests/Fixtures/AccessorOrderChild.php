<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/** @Serializer\AccessorOrder("custom", custom = {"c", "d", "a", "b"}) */
class AccessorOrderChild extends AccessorOrderParent
{
    private $c = 'c', $d = 'd';
}
