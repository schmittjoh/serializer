<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class LazyHandlerRegistry extends HandlerRegistry
{
    /**
     * @var PsrContainerInterface|ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $initializedHandlers = [];

    /**
     * @param PsrContainerInterface|ContainerInterface $container
     * @param array $handlers
     */
    public function __construct($container, array $handlers = [])
    {
        if (!$container instanceof PsrContainerInterface && !$container instanceof ContainerInterface) {
            throw new InvalidArgumentException(sprintf('The container must be an instance of %s or %s (%s given).', PsrContainerInterface::class, ContainerInterface::class, \is_object($container) ? \get_class($container) : \gettype($container)));
        }

        parent::__construct($handlers);
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function registerHandler(int $direction, string $typeName, string $format, $handler): void
    {
        parent::registerHandler($direction, $typeName, $format, $handler);
        unset($this->initializedHandlers[$direction][$typeName][$format]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler(int $direction, string $typeName, string $format)
    {
        if (isset($this->initializedHandlers[$direction][$typeName][$format])) {
            return $this->initializedHandlers[$direction][$typeName][$format];
        }

        if (!isset($this->handlers[$direction][$typeName][$format])) {
            return null;
        }

        $handler = $this->handlers[$direction][$typeName][$format];
        if (\is_array($handler) && \is_string($handler[0]) && $this->container->has($handler[0])) {
            $handler[0] = $this->container->get($handler[0]);
        }

        return $this->initializedHandlers[$direction][$typeName][$format] = $handler;
    }
}
