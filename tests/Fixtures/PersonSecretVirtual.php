<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("NONE")
 * @Serializer\AccessorOrder("custom",custom = {"name", "gender", "age"})
 */
class PersonSecretVirtual
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Exclude()
     */
    public $gender;

    /**
     * @Serializer\Type("string")
     * @Serializer\Expose(if="show_data('age')")
     */
    public $age;

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Type("string")
     * @Serializer\Exclude(if="show_data('gender')")
     */
    public function getGender()
    {
        return $this->gender;
    }
}
