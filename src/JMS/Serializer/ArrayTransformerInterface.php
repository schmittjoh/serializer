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

namespace JMS\Serializer;

/**
 * Interface for array transformation.
 *
 * @author Daniel Bojdo <daniel@bojdo.eu>
 */
interface ArrayTransformerInterface
{
    /**
     * Converts objects to an array structure.
     *
     * This is useful when the data needs to be passed on to other methods which expect array data.
     *
     * @param mixed $data anything that converts to an array, typically an object or an array of objects
     * @param SerializationContext|null $context
     *
     * @return array
     */
    public function toArray($data, SerializationContext $context = null);

    /**
     * Restores objects from an array structure.
     *
     * @param array $data
     * @param string $type
     * @param DeserializationContext|null $context
     *
     * @return mixed this returns whatever the passed type is, typically an object or an array of objects
     */
    public function fromArray(array $data, $type, DeserializationContext $context = null);
}
