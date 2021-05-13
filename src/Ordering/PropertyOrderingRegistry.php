<?php

declare(strict_types=1);

namespace JMS\Serializer\Ordering;

class PropertyOrderingRegistry implements PropertyOrderingRegistryInterface
{
    /**
     * @var PropertiesOrderingInterface[]
     */
    private $orders = [];

    public function add(string $strategyName, PropertiesOrderingInterface $propertyOrdering): void
    {
        if (!\array_key_exists($strategyName, $this->orders)) {
            $this->orders[$strategyName] = $propertyOrdering;
        }
    }

    public function get(string $strategyName): ?PropertiesOrderingInterface
    {
        return $this->orders[$strategyName] ?? null;
    }

    /**
     * @return PropertiesOrderingInterface[]
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
