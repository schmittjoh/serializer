<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\{ProductDescription as Description, ProductName};

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
