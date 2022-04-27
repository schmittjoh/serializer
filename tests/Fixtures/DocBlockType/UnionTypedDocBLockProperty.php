<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType;

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
