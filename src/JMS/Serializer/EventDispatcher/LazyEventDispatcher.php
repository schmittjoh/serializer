<?php

namespace JMS\Serializer\EventDispatcher;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyEventDispatcher extends EventDispatcher
{
    private $container;

    public function __construct($container)
    {
        if (!$container instanceof PsrContainerInterface && !$container instanceof ContainerInterface) {
            throw new \InvalidArgumentException(sprintf('The container must be an instance of %s or %s (%s given).', PsrContainerInterface::class, ContainerInterface::class, \is_object($container) ? \get_class($container) : \gettype($container)));
        }

        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeListeners($eventName, $loweredClass, $format)
    {
        $listeners = parent::initializeListeners($eventName, $loweredClass, $format);

        foreach ($listeners as &$listener) {
            if (!\is_array($listener) || !\is_string($listener[0])) {
                continue;
            }

            if (!$this->container->has($listener[0])) {
                continue;
            }

            $listener[0] = $this->container->get($listener[0]);
        }

        return $listeners;
    }
}
