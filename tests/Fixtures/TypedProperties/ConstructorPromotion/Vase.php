<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\ConstructorPromotion;

class Vase
{
    public function __construct(
        public string $color,
        public ?string $plant = null,
        public string $typeOfSoil = 'potting mix',
        public int $daysSincePotting = -1,
    ) {
    }
}
