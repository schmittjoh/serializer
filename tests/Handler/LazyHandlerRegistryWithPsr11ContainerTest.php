<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
