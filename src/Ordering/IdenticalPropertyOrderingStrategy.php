<?php

declare(strict_types=1);

namespace JMS\Serializer\Ordering;

final class IdenticalPropertyOrderingStrategy implements PropertiesOrderingInterface
{
    /**
     * {@inheritdoc}
     */
    public function order(array $properties, array $options): array
    {
        return $properties;
    }
}
