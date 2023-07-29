<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

/**
 * Handler Registry Interface.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface HandlerRegistryInterface
{
    public function registerSubscribingHandler(SubscribingHandlerInterface $handler): void;

    /**
     * Registers a handler in the registry.
     *
     * @param int $direction one of the GraphNavigatorInterface::DIRECTION_??? constants
     * @param object|callable $handler   function(visitor, mixed $data, array $type): mixed
     */
    public function registerHandler(int $direction, string $typeName, string $format, $handler): void;

    /**
     * @param int $direction one of the GraphNavigatorInterface::DIRECTION_??? constants
     *
     * @return callable|object|null
     */
    public function getHandler(int $direction, string $typeName, string $format);
}
