<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark\Performance;

use JMS\Serializer\Tests\Benchmark\AbstractSerializationBench;

class XmlSerializationBench extends AbstractSerializationBench
{
    protected function getFormat(): string
    {
        return 'xml';
    }
}
