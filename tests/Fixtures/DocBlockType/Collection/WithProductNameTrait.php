<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductName;

trait WithProductNameTrait
{
    /**
     * @var ProductName[]
     */
    public array $productNames;
}
