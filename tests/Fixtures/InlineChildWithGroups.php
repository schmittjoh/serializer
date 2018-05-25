<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

class InlineChildWithGroups
{
    /**
     * @Type("string")
     * @Serializer\Groups({"a"})
     */
    public $a = 'a';

    /**
     * @Type("string")
     * @Serializer\Groups({"b"})
     */
    public $b = 'b';
}
