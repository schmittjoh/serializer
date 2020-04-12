<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class PersonWithAccounts
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("array<JMS\Serializer\Tests\Fixtures\PersonAccount>")
     */
    public $accounts = [];
}
