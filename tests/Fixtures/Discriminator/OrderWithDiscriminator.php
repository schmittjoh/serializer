<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

class OrderWithDiscriminator
{
    /**
     * @var OrderEvent
     * @Serializer\Type(name="JMS\Serializer\Tests\Fixtures\Discriminator\OrderEvent")
     */
    public $event;
}
