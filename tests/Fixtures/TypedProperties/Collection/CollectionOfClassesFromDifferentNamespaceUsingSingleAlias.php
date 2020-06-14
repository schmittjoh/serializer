<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductDescription    as  Description;

class CollectionOfClassesFromDifferentNamespaceUsingSingleAlias
{
    /**
     * @var Description[]
     */
    public array $productDescriptions;
}
