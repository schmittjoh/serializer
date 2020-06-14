<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\Collection;

use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductDescription;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductName;

class IncorrectCollection
{
    /**
     * @var \stdClass
     */
    public array $productIds;
}
