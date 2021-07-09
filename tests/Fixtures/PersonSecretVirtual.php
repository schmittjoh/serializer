<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("NONE")
 * @Serializer\AccessorOrder("custom",custom = {"name", "gender", "age"})
 */
#[Serializer\ExclusionPolicy(policy: 'NONE')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['name', 'gender', 'age'])]
class PersonSecretVirtual
{
    /**
     * @Serializer\Type("string")
     */
    #[Serializer\Type(name: 'string')]
    public $name;

    /**
     * @Serializer\Exclude()
     */
    #[Serializer\Exclude]
    public $gender;

    /**
     * @Serializer\Type("string")
     * @Serializer\Expose(if="show_data('age')")
     */
    #[Serializer\Type(name: 'string')]
    #[Serializer\Expose(if: 'show_data("age")')]
    public $age;

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Type("string")
     * @Serializer\Exclude(if="show_data('gender')")
     */
    #[Serializer\VirtualProperty]
    #[Serializer\Type(name: 'string')]
    #[Serializer\Exclude(if: 'show_data("gender")')]
    public function getGender()
    {
        return $this->gender;
    }
}
