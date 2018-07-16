<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithEmptyArrayAndHash
{
    /**
     * @Serializer\Type("array<string,string>")
     * @Serializer\SkipWhenEmpty()
     */
    private $hash = [];
    /**
     * @Serializer\Type("array<string>")
     * @Serializer\SkipWhenEmpty()
     */
    private $array = [];

    /**
     * @Serializer\SkipWhenEmpty()
     */
    private $object = [];

    public function __construct()
    {
        $this->object = new InlineChildEmpty();
    }
}
