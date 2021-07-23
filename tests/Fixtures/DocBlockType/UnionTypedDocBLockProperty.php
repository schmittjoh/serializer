<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType;

use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductDescription;

class UnionTypedDocBLockProperty
{
    /**
     * @var int|string
     */
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
