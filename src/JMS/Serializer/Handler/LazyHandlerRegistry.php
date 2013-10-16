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

namespace JMS\Serializer\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyHandlerRegistry extends HandlerRegistry
{
    private $container;
    private $initializedHandlers = array();

    public function __construct(ContainerInterface $container, array $handlers = array())
    {
        parent::__construct($handlers);
        $this->container = $container;
    }

    public function registerHandler($direction, $typeName, $format, $handler)
    {
        parent::registerHandler($direction, $typeName, $format, $handler);
        unset($this->initializedHandlers[$direction][$typeName][$format]);
    }

    public function getHandler($direction, $typeName, $format)
    {
        if (isset($this->initializedHandlers[$direction][$typeName][$format])) {
            return $this->initializedHandlers[$direction][$typeName][$format];
        }

        if ( ! isset($this->handlers[$direction][$typeName][$format])) {
            $matchedParent = $this->matchParent($typeName, $direction, $format);

            if (false !== $matchedParent) {
                $matchedParent[0] = $this->container->get($matchedParent[0]);

                return $this->initializedHandlers[$direction][$typeName][$format] = $matchedParent;
            }

            $matchedInterface = $this->matchInterface($typeName, $direction, $format);

            if (false !== $matchedInterface) {
                $matchedInterface[0] = $this->container->get($matchedInterface[0]);

                return $this->initializedHandlers[$direction][$typeName][$format] = $matchedInterface;
            }

            return null;
        }

        $handler = $this->handlers[$direction][$typeName][$format];
        if (is_array($handler) && is_string($handler[0]) && $this->container->has($handler[0])) {
            $handler[0] = $this->container->get($handler[0]);
        }

        return $this->initializedHandlers[$direction][$typeName][$format] = $handler;
    }

    /**
     * Match a parent class
     *
     * @param string $typeName
     * @param string $direction
     * @param string $format
     *
     * @return mixed
     */
    protected function matchParent($typeName, $direction, $format)
    {
        try {
            $class   = new \ReflectionClass($typeName);
            $parents = array();

            while ($parent = $class->getParentClass()) {
                $parentClassName = $parent->getName();

                foreach ($this->handlers[$direction] as $knownTypeName => $formatMap) {
                    if ($knownTypeName === $parentClassName) {

                        return $formatMap[$format];
                    }
                }

                $matchedInterface = $this->matchInterface($parentClassName, $direction, $format);

                if (false !== $matchedInterface) {
                    return $matchedInterface;
                }

                $class = $parent;
            }
        } catch (\ReflectionException $e) {}

        return false;
    }

    /**
     * Match an interface
     *
     * @param string $typeName
     * @param string $direction
     * @param string $format
     *
     * @return mixed
     */
    protected function matchInterface($typeName, $direction, $format)
    {
        try {
            $class         = new \ReflectionClass($typeName);
            $parents       = array();
            $interfaceList = $class->getInterfaces();

            foreach ($interfaceList as $reflectedClass) {
                $interfaceName = $reflectedClass->getName();

                $matchedParent = $this->matchParent($interfaceName, $direction, $format);

                if (false !== $matchedParent) {
                    return $matchedParent;
                }

                foreach ($this->handlers[$direction] as $knownTypeName => $formatMap) {
                    if ($knownTypeName === $interfaceName) {

                        return $formatMap[$format];
                    }
                }
            }
        } catch (\ReflectionException $e) {}

        return false;
    }
}
