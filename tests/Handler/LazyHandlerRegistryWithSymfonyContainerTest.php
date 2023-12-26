<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use Symfony\Component\DependencyInjection\Container;

class LazyHandlerRegistryWithSymfonyContainerTest extends LazyHandlerRegistryTestCase
{
    protected function createContainer()
    {
        return new Container();
    }

    protected function registerHandlerService($serviceId, $listener)
    {
        $this->container->set($serviceId, $listener);
    }
}
