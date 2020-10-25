<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithEmptyArrayAndHashNotAnnotated
{
    /**
     * @Serializer\Type("array<string,string>")
     */
    private $hash = [];
    /**
     * @Serializer\Type("array<string>")
     */
    private $array = [];

    private $object = [];

    public function __construct()
    {
        $this->object = new InlineChildEmpty();
    }
}
