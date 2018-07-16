<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\VirtualProperty("firstName", exp="object.getFirstName()", options={@Serializer\SerializedName("my_first_name")})
 */
class AuthorExpressionAccess
{
    private $id;
    /**
     * @Serializer\Exclude()
     */
    private $firstName;

    /**
     * @Serializer\Exclude()
     */
    private $lastName;

    public function __construct($id, $firstName, $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}
