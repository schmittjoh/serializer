<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductDescription;

class CollectionOfClassesFromDifferentNamespace
{
    /**
     * @var ProductDescription[]
     */
    public array $productDescriptions;
}
