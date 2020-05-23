<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Exclude(if="object.expired")
 */
class PersonAccountWithParent extends PersonAccountParent
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("boolean")
     */
    public $expired;
}
