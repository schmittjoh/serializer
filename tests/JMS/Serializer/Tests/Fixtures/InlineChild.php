<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

class InlineChild
{
    /**
     * @Type("string")
     */
    private $a = 'a';

    /**
     * @Type("string")
     */
    private $b = 'b';
}