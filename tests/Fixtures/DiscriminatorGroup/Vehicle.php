<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DiscriminatorGroup;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", groups={"foo"}, map = {
 *    "car": "JMS\Serializer\Tests\Fixtures\DiscriminatorGroup\Car"
 * })
 */
#[Serializer\Discriminator(field: 'type', groups: ['foo'], map: ['car' => 'JMS\Serializer\Tests\Fixtures\DiscriminatorGroup\Car'])]
abstract class Vehicle
{
    /**
     * @Serializer\Type("integer")
     * @Serializer\Groups({"foo"})
     */
    #[Serializer\Type(name: 'integer')]
    #[Serializer\Groups(groups: ['foo'])]
    public $km;

    public function __construct($km)
    {
        $this->km = (int) $km;
    }
}
