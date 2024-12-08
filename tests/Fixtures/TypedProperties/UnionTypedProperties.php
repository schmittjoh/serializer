<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

class UnionTypedProperties
{
    private bool|float|string|array|int $data;

    private int|bool|float|string|null $nullableData;

    private string|false $valueTypedUnion;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
