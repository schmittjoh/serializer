<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", nullOnUnknown=true, map = {
 *    "paid": "JMS\Serializer\Tests\Fixtures\Discriminator\OrderPaidEvent"
 * })
 */
abstract class OrderEvent
{
}
