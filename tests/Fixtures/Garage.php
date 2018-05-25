<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class Garage
{
    /**
     * @Type("array<JMS\Serializer\Tests\Fixtures\Discriminator\Vehicle>")
     */
    public $vehicles;

    public function __construct($vehicles)
    {
        $this->vehicles = $vehicles;
    }
}
