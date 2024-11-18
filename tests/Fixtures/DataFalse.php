<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

class DataFalse
{
    public false $data;

    public function __construct(
        false $data
    ) {
        $this->data = $data;
    }
}
