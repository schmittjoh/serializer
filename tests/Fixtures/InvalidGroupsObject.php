<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

class InvalidGroupsObject
{
    /**
     * @Groups({"foo, bar"})
     * @Type("string")
     */
    private $foo;
}
