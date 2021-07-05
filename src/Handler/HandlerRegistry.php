<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigatorInterface;

class HandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var callable[]
     */
    protected $handlers;

    public static function getDefaultMethod(int $direction, string $type, string $format): string
    {
        if (false !== $pos = strrpos($type, '\\')) {
            $type = substr($type, $pos + 1);
        }

        switch ($direction) {
            case GraphNavigatorInterface::DIRECTION_DESERIALIZATION:
                return 'deserialize' . $type . 'From' . $format;

            case GraphNavigatorInterface::DIRECTION_SERIALIZATION:
                return 'serialize' . $type . 'To' . $format;

            default:
                throw new LogicException(sprintf('The direction %s does not exist; see GraphNavigatorInterface::DIRECTION_??? constants.', json_encode($direction)));
        }
    }

    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    public function registerSubscribingHandler(SubscribingHandlerInterface $handler): void
    {
        foreach ($handler->getSubscribingMethods() as $methodData) {
            if (!isset($methodData['type'], $methodData['format'])) {
                throw new RuntimeException(sprintf('For each subscribing method a "type" and "format" attribute must be given, but only got "%s" for %s.', implode('" and "', array_keys($methodData)), \get_class($handler)));
            }

            $directions = [GraphNavigatorInterface::DIRECTION_DESERIALIZATION, GraphNavigatorInterface::DIRECTION_SERIALIZATION];
            if (isset($methodData['direction'])) {
                $directions = [$methodData['direction']];
            }

            foreach ($directions as $direction) {
                $method = $methodData['method'] ?? self::getDefaultMethod($direction, $methodData['type'], $methodData['format']);
                $this->registerHandler($direction, $methodData['type'], $methodData['format'], [$handler, $method]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerHandler(int $direction, string $typeName, string $format, $handler): void
    {
        $this->handlers[$direction][$typeName][$format] = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler(int $direction, string $typeName, string $format)
    {
        if (!isset($this->handlers[$direction][$typeName][$format])) {
            return null;
        }

        return $this->handlers[$direction][$typeName][$format];
    }

    /**
     * @internal Used for profiling
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}
