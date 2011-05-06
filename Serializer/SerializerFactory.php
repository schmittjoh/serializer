<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer;

use JMS\SerializerBundle\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SerializerFactory
{
    private $container;
    private $serializerMap = array();

    public function __construct(ContainerInterface $container, $serializerMap = array())
    {
        $this->container = $container;
        $this->serializerMap = $serializerMap;
    }

    public function getSerializer($version = null)
    {
        if (null === $version) {
            return $this->container->get('serializer');
        }

        if (!isset($this->serializerMap[$version])) {
            throw new RuntimeException(sprintf('There was no serializer configured for version "%s".', $version));
        }

        return $this->container->get($this->serializerMap[$version]);
    }
}