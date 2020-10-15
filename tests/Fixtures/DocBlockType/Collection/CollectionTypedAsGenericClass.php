<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

class CollectionTypedAsGenericClass
{
    /**
     * @var array<Product>
     */
    public array $productIds;
}
