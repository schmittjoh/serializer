<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation as Serializer;
use JMS\SerializerBundle\Annotation\Type;

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