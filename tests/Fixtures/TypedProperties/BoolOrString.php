<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

class BoolOrString
{
    public bool|string $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
