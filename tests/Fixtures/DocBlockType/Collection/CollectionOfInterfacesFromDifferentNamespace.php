<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductColor;

class CollectionOfInterfacesFromDifferentNamespace
{
    /**
     * @var ProductColor[]
     */
    public array $productColors;
}
