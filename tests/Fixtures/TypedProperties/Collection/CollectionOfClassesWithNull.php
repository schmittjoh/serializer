<?php

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

class CollectionOfClassesWithNull
{
    /**
     * @var Product[]|null
     */
    public ?array $productIds;
}