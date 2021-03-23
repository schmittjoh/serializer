<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

class OrderWithDiscriminator
{
    /**
     * @Serializer\Type(name="JMS\Serializer\Tests\Fixtures\Discriminator\OrderEvent")
     *
     * @var OrderEvent
     */
    public $event;
}
