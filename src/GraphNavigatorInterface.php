<?php

declare(strict_types=1);

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

namespace JMS\Serializer;

use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Type\TypeDefinition;

interface GraphNavigatorInterface
{
    const DIRECTION_SERIALIZATION = 1;
    const DIRECTION_DESERIALIZATION = 2;

    /**
     * Called at the beginning of the serialization process. The navigator should use the traverse the object graph
     * and pass to the $visitor the value of found nodes (following the rules obtained from $context).
     *
     * @param VisitorInterface $visitor
     * @param Context $context
     */
    public function initialize(VisitorInterface $visitor, Context $context): void;

    /**
     * Called for each node of the graph that is being traversed.
     *
     * @throws NotAcceptableException
     * @param mixed $data the data depends on the direction, and type of visitor
     * @param null|array $type array has the format ["name" => string, "params" => array]
     * @return mixed the return value depends on the direction, and type of visitor
     */
    public function accept($data, TypeDefinition $type = null);
}
