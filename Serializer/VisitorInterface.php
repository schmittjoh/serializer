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

use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;

interface VisitorInterface
{
    function prepare($data);
    function visitString($data, $type);
    function visitBoolean($data, $type);
    function visitDouble($data, $type);
    function visitInteger($data, $type);
    function visitUsingCustomHandler($data, $type, &$visited);
    function visitArray($data, $type);
    function visitTraversable($data, $type);
    function startVisitingObject(ClassMetadata $metadata, $data, $type);
    function visitProperty(PropertyMetadata $metadata, $data);
    function endVisitingObject(ClassMetadata $metadata, $data, $type);
    function visitPropertyUsingCustomHandler(PropertyMetadata $metadata, $object);
    function setNavigator(GraphNavigator $navigator);
    function getNavigator();
    function getResult();
}