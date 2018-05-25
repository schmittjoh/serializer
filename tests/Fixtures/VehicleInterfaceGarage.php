<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class VehicleInterfaceGarage
{
    /**
     * @Type("array<JMS\Serializer\Tests\Fixtures\Discriminator\VehicleInterface>")
     */
    public $vehicles;

    public function __construct($vehicles)
    {
        $this->vehicles = $vehicles;
    }
}
