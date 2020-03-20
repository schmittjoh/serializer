<?php
namespace JMS\Serializer\Tests\Fixtures\Discriminator\Serialization;

use JMS\Serializer\Annotation as JMS;

class ExtendedUser extends User
{
    /**
     * @JMS\Type("string")
     * @JMS\Groups({"base"})
     * @var string
     */
    public $extendAttribute;

    /**
     * ExtendedUser constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param string $extendAttribute
     */
    public function __construct($id, $name, $description, $extendAttribute)
    {
        parent::__construct($id, $name, $description);
        $this->extendAttribute = $extendAttribute;
    }
}
