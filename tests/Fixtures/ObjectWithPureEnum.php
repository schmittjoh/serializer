<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

final class ObjectWithPureEnum
{
    private PureEnum $enum;

    public function __construct(PureEnum $enum)
    {
        $this->enum = $enum;
    }
}
