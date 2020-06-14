<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

class CollectionOfNotExistingClasses
{
    /**
     * @var NotExistingClass[]
     */
    public array $productIds;
}
