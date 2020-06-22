<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithStringKeyMap
{
    /**
     * @Serializer\Type("array<string,string>")
     * @Serializer\XmlMap(keyAttribute="key", valueAttribute="value")
     */
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }
}
