<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

class MapTypedAsGenericClass
{
    /**
     * @var array<int, Product>
     */
    public array $productIds;

    /**
     * @var array<Product|Vehicle>
     */
    public array $productOrVehicleIds;

    /**
     * @var array<int, Product|Vehicle>
     */
    public array $productOrVehicleIdsWithKey;

    /**
     * @var array<Product|Vehicle[]>
     */
    public array $productOrVehicles;
}
