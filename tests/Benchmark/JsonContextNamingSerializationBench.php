<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;

class JsonContextNamingSerializationBench extends JsonSerializationBench
{
    protected function createContext(): SerializationContext
    {
        return parent::createContext()->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
    }
}
