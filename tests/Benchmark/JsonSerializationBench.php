<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark;

class JsonSerializationBench extends AbstractSerializationBench
{
    protected function getFormat(): string
    {
        return 'json';
    }
}
