<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

class DataFalse
{
    public function __construct(
        public false $data
    ) {
    }
}
