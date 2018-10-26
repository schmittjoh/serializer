<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

class InlineChildWithGroups
{
    /**
     * @Serializer\Groups({"a"})
     *
     * @Type("string")
     */
    public $a = 'a';

    /**
     * @Serializer\Groups({"b"})
     *
     * @Type("string")
     */
    public $b = 'b';
}
