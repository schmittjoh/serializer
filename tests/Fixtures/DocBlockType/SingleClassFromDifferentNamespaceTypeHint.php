<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType;

use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductDescription;

class SingleClassFromDifferentNamespaceTypeHint
{
    /**
     * @var ProductDescription
     */
    public $data;
}
