<?php

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductName;

trait WithProductNameTrait
{
    /**
     * @var ProductName[]
     */
    public array $productNames;
}