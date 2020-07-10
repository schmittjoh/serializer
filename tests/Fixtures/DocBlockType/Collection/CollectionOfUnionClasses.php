<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductDescription;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductName;

class CollectionOfUnionClasses
{
    /**
     * @var ProductName[]|ProductDescription[]
     */
    public array $productIds;
}
