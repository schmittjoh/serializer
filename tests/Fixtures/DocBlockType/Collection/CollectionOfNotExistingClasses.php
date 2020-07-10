<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

class CollectionOfNotExistingClasses
{
    /**
     * @var NotExistingClass[]
     */
    public array $productIds;
}
