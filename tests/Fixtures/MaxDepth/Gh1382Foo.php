<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\MaxDepth;

use JMS\Serializer\Annotation as Serializer;

class Gh1382Foo
{
    /**
     * @Serializer\MaxDepth(0)
     */
    #[Serializer\MaxDepth(depth: 0)]
    public $a;

    public function __construct()
    {
        $this->a = new Gh1382Bar();
        $this->a->c = new Gh1382Bar();
    }
}
