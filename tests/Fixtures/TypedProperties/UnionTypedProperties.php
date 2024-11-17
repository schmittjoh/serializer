<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

class UnionTypedProperties
{
    private int|bool|float|string|array $data;

    private int|bool|float|string|null $nullableData;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
