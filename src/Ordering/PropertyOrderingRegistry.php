<?php

declare(strict_types=1);

namespace JMS\Serializer\Ordering;

class PropertyOrderingRegistry implements PropertyOrderingRegistryInterface
{
    /**
     * @var PropertyOrderingInterface[]
     */
    private $orders = [];

    public function add(string $strategyName, PropertyOrderingInterface $propertyOrdering): void
    {
        if (!\array_key_exists($strategyName, $this->orders)) {
            $this->orders[$strategyName] = $propertyOrdering;
        }
    }

    public function get(string $strategyName): ?PropertyOrderingInterface
    {
        return $this->orders[$strategyName] ?? null;
    }

    /**
     * @return PropertyOrderingInterface[]
     */
    public function all(): array
    {
        return $this->orders;
    }

    public function supports(string $strategyName): bool
    {
        return \array_key_exists($strategyName, $this->orders);
    }
}
