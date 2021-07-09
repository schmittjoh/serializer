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
    #[Serializer\Groups(groups: ['a'])]
    #[Type(name: 'string')]
    public $a = 'a';

    /**
     * @Serializer\Groups({"b"})
     *
     * @Type("string")
     */
    #[Serializer\Groups(groups: ['b'])]
    #[Type(name: 'string')]
    public $b = 'b';
}
