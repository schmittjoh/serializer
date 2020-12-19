<?php

declare(strict_types=1);

namespace JMS\Serializer\Ordering;

/**
 * Interface for register PropertyOrdering strategy
 */
interface PropertyOrderingRegistryInterface
{
    public function add(string $strategyName, PropertyOrderingInterface $propertyOrdering);

    public function get(string $strategyName): ?PropertyOrderingInterface;

    public function supports(string $strategyName): bool;
}
