<?php

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
    public function registerSubscribingHandler(SubscribingHandlerInterface $handler);

    /**
     * Registers a handler in the registry.
     *
     * @param integer $direction one of the GraphNavigator::DIRECTION_??? constants
     * @param string $typeName
     * @param string $format
     * @param callable $handler function(VisitorInterface, mixed $data, array $type): mixed
     *
     * @return void
     */
    public function registerHandler($direction, $typeName, $format, $handler);

    /**
     * @param integer $direction one of the GraphNavigator::DIRECTION_??? constants
     * @param string $typeName
     * @param string $format
     *
     * @return callable|null
     */
    public function getHandler($direction, $typeName, $format);
}
