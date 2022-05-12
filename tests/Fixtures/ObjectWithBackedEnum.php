<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

final class ObjectWithBackedEnum
{
    private BackedEnum $enum;

    public function __construct(BackedEnum $enum)
    {
        $this->enum = $enum;
    }
}
