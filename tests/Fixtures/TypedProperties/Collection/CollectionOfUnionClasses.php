<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductDescription;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductName;

class CollectionOfUnionClasses
{
    /**
     * @var ProductName[]|ProductDescription[]
     */
    public array $productIds;
}
