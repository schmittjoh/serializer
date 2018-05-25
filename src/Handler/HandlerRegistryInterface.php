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
    /**
     * @param SubscribingHandlerInterface $handler
     *
     * @return void
     */
    public function registerSubscribingHandler(SubscribingHandlerInterface $handler): void;

    /**
     * Registers a handler in the registry.
     *
     * @param integer $direction one of the GraphNavigatorInterface::DIRECTION_??? constants
     * @param string $typeName
     * @param string $format
     * @param callable $handler function(visitor, mixed $data, array $type): mixed
     *
     * @return void
     */
    public function registerHandler(int $direction, string $typeName, string $format, $handler): void;

    /**
     * @param integer $direction one of the GraphNavigatorInterface::DIRECTION_??? constants
     * @param string $typeName
     * @param string $format
     *
     * @return callable|null
     */
    public function getHandler(int $direction, string $typeName, string $format);
}
