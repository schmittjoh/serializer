<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

class CollectionOfClassesWithFullNamespacePath
{
    /**
     * @var JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Product[]
     */
    public array $productIds;
}
