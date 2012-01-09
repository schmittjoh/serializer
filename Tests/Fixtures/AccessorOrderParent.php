<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation as Serializer;

/** @Serializer\AccessorOrder("alphabetical") */
class AccessorOrderParent
{
    private $b = 'b', $a = 'a';
}