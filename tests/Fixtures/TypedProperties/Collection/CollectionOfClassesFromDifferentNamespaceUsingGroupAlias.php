<?php

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\{
    ProductName,
    ProductDescription
    as Description
};

class CollectionOfClassesFromDifferentNamespaceUsingGroupAlias
{
    /**
     * @var Description[]
     */
    public array $productDescriptions;
    /**
     * @var ProductName[]
     */
    public array $productNames;
}