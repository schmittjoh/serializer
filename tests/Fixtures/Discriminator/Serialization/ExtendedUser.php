<?php

declare(strict_types=1);

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

    public function __construct($id, $name, $description, $extendAttribute)
    {
        parent::__construct($id, $name, $description);
        $this->extendAttribute = $extendAttribute;
    }
}
