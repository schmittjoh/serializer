<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

class CollectionOfClassesWithNull
{
    /**
     * @var Product[]|null
     */
    public ?array $productIds;
}
