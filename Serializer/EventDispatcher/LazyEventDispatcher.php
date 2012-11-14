<?php

namespace JMS\SerializerBundle\Serializer\EventDispatcher;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyEventDispatcher extends EventDispatcher
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function initializeListeners($eventName, $loweredClass, $format)
    {
        $listeners = parent::initializeListeners($eventName, $loweredClass, $format);

        foreach ($listeners as &$listener) {
            if ( ! is_array($listener) || ! is_string($listener[0])) {
                continue;
            }

            if ( ! $this->container->has($listener[0])) {
                continue;
            }

            $listener[0] = $this->container->get($listener[0]);
        }

        return $listeners;
    }
}
