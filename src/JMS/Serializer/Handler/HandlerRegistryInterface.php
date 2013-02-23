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

/**
 * Handler Registry Interface.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface HandlerRegistryInterface
{
    /**
     * @param SubscribingHandlerInterface $handler
     *
     * @return void
     */
    public function registerSubscribingHandler(SubscribingHandlerInterface $handler);

    /**
     * Registers a handler in the registry.
     *
     * @param integer $direction one of the GraphNavigator::DIRECTION_??? constants
     * @param string $typeName
     * @param string $format
     * @param callable $handler function(VisitorInterface, mixed $data, array $type): mixed
     *
     * @return void
     */
    public function registerHandler($direction, $typeName, $format, $handler);

    /**
     * @param integer $direction one of the GraphNavigator::DIRECTION_??? constants
     * @param string $typeName
     * @param string $format
     *
     * @return callable|null
     */
    public function getHandler($direction, $typeName, $format);
}
