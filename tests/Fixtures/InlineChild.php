<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class InlineChild
{
    /**
     * @Type("string")
     */
    public $a = 'a';

    /**
     * @Type("string")
     */
    public $b = 'b';
}
