<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark\Performance;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\Handler\JsonSerializableHandler;
use JMS\Serializer\Tests\Benchmark\AbstractSerializationBench;
use JMS\Serializer\Tests\Fixtures\Author;

class JsonSerializableBench extends AbstractSerializationBench
{
    protected function getFormat(): string
    {
        return 'json';
    }

    protected function configureHandlers(HandlerRegistryInterface $handlerRegistry)
    {
        $handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, Author::class, 'json', new JsonSerializableHandler());
    }
}
