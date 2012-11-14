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

/**
 * Interface for visitors.
 *
 * This contains the minimal set of values that must be supported for any
 * output format.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface VisitorInterface
{
    /**
     * Allows visitors to convert the input data to a different representation
     * before the actual serialization/deserialization process starts.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function prepare($data);

    /**
     * Controls whether keys will be preserved when serializing null values
     *
     * @param bool $serializeNull
     * @return void
     */
    public function setSerializeNull($serializeNull);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitNull($data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitString($data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitBoolean($data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitDouble($data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitInteger($data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitArray($data, array $type);

    /**
     * Called before the properties of the object are being visited.
     *
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param array $type
     *
     * @return void
     */
    public function startVisitingObject(ClassMetadata $metadata, $data, array $type);

    /**
     * @param PropertyMetadata $metadata
     * @param mixed $data
     *
     * @return void
     */
    public function visitProperty(PropertyMetadata $metadata, $data);

    /**
     * Called after all properties of the object have been visited.
     *
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function endVisitingObject(ClassMetadata $metadata, $data, array $type);

    /**
     * Called before serialization/deserialization starts.
     *
     * @param GraphNavigator $navigator
     *
     * @return void
     */
    public function setNavigator(GraphNavigator $navigator);

    /**
     * @return GraphNavigator
     */
    public function getNavigator();

    /**
     * @return object|array|scalar
     */
    public function getResult();
}
