<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use Psr\Container\ContainerInterface;

class LazyHandlerRegistryWithPsr11ContainerTest extends LazyHandlerRegistryTest
{
    protected function createContainer()
    {
        return new Psr11Container();
    }

    protected function registerHandlerService($serviceId, $listener)
    {
        $this->container->set($serviceId, $listener);
    }
}

class Psr11Container implements ContainerInterface
{
    private $services;

    public function get($id)
    {
        return $this->services[$id];
    }

    public function has($id)
    {
        return isset($this->services[$id]);
    }

    public function set($id, $service)
    {
        $this->services[$id] = $service;
    }
}
