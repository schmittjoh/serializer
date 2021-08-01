<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", map = {
 *    "car": "JMS\Serializer\Tests\Fixtures\TypedProperties\Car",
 * })
 */
#[Serializer\Discriminator(field: 'type', map: ['car' => 'JMS\Serializer\Tests\Fixtures\TypedProperties\Car'])]
interface Vehicle
{
}
