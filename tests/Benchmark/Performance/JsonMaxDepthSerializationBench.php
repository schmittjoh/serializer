<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark\Performance;

use JMS\Serializer\SerializationContext;

class JsonMaxDepthSerializationBench extends JsonSerializationBench
{
    protected function createContext(): SerializationContext
    {
        return parent::createContext()->enableMaxDepthChecks();
    }
}
