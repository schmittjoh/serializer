<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectUsingTypeCasting
{
    /**
     * @var ObjectWithToString
     * @Serializer\Type("string")
     */
    public $asString;
}
