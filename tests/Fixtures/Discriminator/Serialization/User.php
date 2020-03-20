<?php


namespace JMS\Serializer\Tests\Fixtures\Discriminator\Serialization;

use JMS\Serializer\Annotation as JMS;

/**
 * Class User
 *
 * @package JMS\Serializer\Tests\Fixtures\Discriminator\Serialization
 * @JMS\Discriminator(field = "entityName",
 *     groups={"entity.identification"},
 *     map = {
 *     "User": "JMS\Serializer\Tests\Fixtures\Discriminator\Serialization\User",
 *     "ExtendedUser": "JMS\Serializer\Tests\Fixtures\Discriminator\Serialization\ExtendedUser"
 * })
 */
class User extends Entity
{
    /**
     * @JMS\Type("string")
     * @JMS\Groups({"base"})
     * @var string
     */
    public $name;
    /**
     * @JMS\Type("string")
     * @JMS\Groups({"base"})
     * @var string
     */
    public $description;

    /**
     * User constructor.
     *
     * @param        $id
     * @param string $name
     * @param string $description
     */
    public function __construct($id, $name, $description)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->description = $description;
    }


}
