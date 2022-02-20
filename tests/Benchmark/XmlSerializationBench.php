<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark;

class XmlSerializationBench extends AbstractSerializationBench
{
    protected function getFormat(): string
    {
        return 'xml';
    }
}
