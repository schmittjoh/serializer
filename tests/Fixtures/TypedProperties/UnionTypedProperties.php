<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

class UnionTypedProperties
{
    public bool|float|string|array|int $data;

    private int|bool|float|string|null $nullableData;

    private string|false $valueTypedUnion;

    public function __construct($data, $nullableData, $valueTypedUnion)
    {
        $this->data = $data;
        $this->nullableData = $nullableData;
        $this->valueTypedUnion = $valueTypedUnion;
    }
}
