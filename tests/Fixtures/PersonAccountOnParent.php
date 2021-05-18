<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class PersonAccountOnParent extends PersonAccountParentWithExclude
{
    /**
     * @Serializer\Type("string")
     */
    #[Serializer\Type(name: 'string')]
    public $name;
}
