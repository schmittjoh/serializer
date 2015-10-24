<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

namespace JMS\Serializer\EventDispatcher;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyEventDispatcher extends EventDispatcher
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
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
