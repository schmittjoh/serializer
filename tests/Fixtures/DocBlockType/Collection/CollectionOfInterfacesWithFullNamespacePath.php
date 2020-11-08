<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

class CollectionOfInterfacesWithFullNamespacePath
{
    /**
     * @var JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductColor[]
     */
    public array $productColors;
}
