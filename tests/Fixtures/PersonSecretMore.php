<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("ALL")
 * @Serializer\AccessorOrder("custom",custom = {"name", "gender"})
 */
#[Serializer\ExclusionPolicy(policy: 'ALL')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['name', 'gender'])]
class PersonSecretMore
{
    /**
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    #[Serializer\Type(name: 'string')]
    #[Serializer\Expose]
    public $name;

    /**
     * @Serializer\Type("string")
     * @Serializer\Expose(if="show_data('gender')")
     */
    #[Serializer\Type(name: 'string')]
    #[Serializer\Expose(if: 'show_data("gender")')]
    public $gender;
}
