<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

class MapTypedAsGenericClass
{
    /**
     * @var array<int, Product>
     */
    public array $productIds;
}
