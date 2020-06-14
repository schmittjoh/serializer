<?php

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductDescription;

class CollectionOfClassesFromDifferentNamespace
{
    /**
     * @var ProductDescription[]
     */
    public array $productDescriptions;
}