<?php

declare(strict_types=1);

namespace JMS\Serializer\Ordering;

final class CustomPropertyOrderingStrategy implements PropertyOrderingInterface
{
    /**
     * {@inheritdoc}
     */
    public function order(array $properties, array $options): array
    {
        /** @var int[] $ordering property => weight */
        $ordering = $options['ordering'] ?? [];

        $currentSorting = $properties ? array_combine(array_keys($properties), range(1, \count($properties))) : [];

        uksort($properties, static function ($a, $b) use ($currentSorting, $ordering) {
            $existsA = isset($ordering[$a]);
            $existsB = isset($ordering[$b]);

            if (!$existsA && !$existsB) {
                return $currentSorting[$a] - $currentSorting[$b];
            }

            if (!$existsA) {
                return 1;
            }

            if (!$existsB) {
                return -1;
            }

            return $ordering[$a] < $ordering[$b] ? -1 : 1;
        });

        return $properties;
    }
}
