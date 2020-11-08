<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

class CollectionOfInterfacesFromGlobalNamespace
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
     * @var \JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductColor[]
     */
    public array $productColors;
}
