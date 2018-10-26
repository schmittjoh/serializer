<?php

declare(strict_types=1);

namespace JMS\Serializer\Ordering;

use JMS\Serializer\Metadata\PropertyMetadata;

final class AlphabeticalPropertyOrderingStrategy implements PropertyOrderingInterface
{
    /**
     * {@inheritdoc}
     */
    public function order(array $properties): array
    {
        uasort(
            $properties,
            static function (PropertyMetadata $a, PropertyMetadata $b): int {
                return strcmp($a->name, $b->name);
            }
        );

        return $properties;
    }
}
