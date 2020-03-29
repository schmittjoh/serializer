<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("NONE")
 * @Serializer\AccessorOrder("custom",custom = {"name", "permissions" ,"userAgent"})
 */
class PersonPermission
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     * @Serializer\ReadOnly(if="user.isAdmin()")
     */
    public $permissions;

    /**
     * @Serializer\Type("string")
     * @Serializer\ReadOnly(if="!(user.isAdmin())")
     */
    public $userAgent;
}
