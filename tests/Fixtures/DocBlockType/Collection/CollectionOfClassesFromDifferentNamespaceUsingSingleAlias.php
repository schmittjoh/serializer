<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductDescription as Description;

class CollectionOfClassesFromDifferentNamespaceUsingSingleAlias
{
    /**
     * @var Description[]
     */
    public array $productDescriptions;
}
