<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectUsingTypeCasting
{
    /**
     * @Serializer\Type("string")
     *
     * @var ObjectWithToString
     */
    public $asString;
}
