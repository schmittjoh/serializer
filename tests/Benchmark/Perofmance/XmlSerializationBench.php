<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark\Perofmance;

use JMS\Serializer\Tests\Benchmark\AbstractSerializationBench;

class XmlSerializationBench extends AbstractSerializationBench
{
    protected function getFormat(): string
    {
        return 'xml';
    }
}
