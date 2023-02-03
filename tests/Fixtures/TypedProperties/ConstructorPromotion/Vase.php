<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\ConstructorPromotion;

use JMS\Serializer\Annotation as JMS;

class Vase
{
    public function __construct(
        public string $color,
        #[JMS\Accessor(setter: 'setSize')]
        public string $size,
        public ?string $plant = null,
        public string $typeOfSoil = 'potting mix',
        public int $daysSincePotting = -1,
        #[JMS\Accessor(setter: 'setWeight')]
        public int $weight = 10,
    ) {
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight + 30;
    }

    public function setSize(string $size): void
    {
        if ('big' === $size) {
            $this->size = 'huge';

            return;
        }

        $this->size = $size;
    }
}
