<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

class FalseOrString
{
    public false|string $data;

    public function __construct(false|string $data)
    {
        $this->data = $data;
    }
}
