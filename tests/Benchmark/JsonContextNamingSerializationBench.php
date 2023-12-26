<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Tests\Benchmark\Performance\JsonSerializationBench;

class JsonContextNamingSerializationBench extends JsonSerializationBench
{
    protected function createContext(): SerializationContext
    {
        /** @phpstan-ignore-next-line */
        return parent::createContext()->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
    }
}
