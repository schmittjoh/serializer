<?php

declare(strict_types=1);

namespace JMS\Serializer\Ordering;

/**
 * Interface for register PropertiesOrderingInterface strategy
 */
interface PropertyOrderingRegistryInterface
{
    public function add(string $strategyName, PropertiesOrderingInterface $propertyOrdering);

    public function get(string $strategyName): ?PropertiesOrderingInterface;

    public function supports(string $strategyName): bool;
}
