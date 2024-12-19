<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

class UnionTypedProperties
{
    private int|bool|float|string|array $data;

    private int|bool|float|string|null $nullableData = null;

    private string|false $valueTypedUnion;

    public function __construct($data, $nullableData, $valueTypedUnion)
    {
        $this->data = $data;
        $this->nullableData = $nullableData;
        $this->valueTypedUnion = $valueTypedUnion;
    }
}
