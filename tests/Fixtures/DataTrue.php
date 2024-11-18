<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

class DataTrue
{
    public true $data;

    public function __construct(
        true $data
    ) {
        $this->data = $data;
    }
}
