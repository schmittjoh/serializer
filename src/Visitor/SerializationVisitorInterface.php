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

namespace JMS\Serializer\Visitor;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Type\TypeDefinition;
use JMS\Serializer\VisitorInterface;

/**
 * Interface for visitors.
 *
 * This contains the minimal set of values that must be supported for any
 * output format.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface SerializationVisitorInterface extends VisitorInterface
{
    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitNull($data, TypeDefinition $type);

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitString(string $data, TypeDefinition $type);

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitBoolean(bool $data, TypeDefinition $type);

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitDouble(float $data, TypeDefinition $type);

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitInteger(int $data, TypeDefinition $type);

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitArray(array $data, TypeDefinition $type);

    /**
     * Called before the properties of the object are being visited.
     *
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return void
     */
    public function startVisitingObject(ClassMetadata $metadata, object $data, TypeDefinition $type): void;

    /**
     * @param PropertyMetadata $metadata
     * @param mixed $data
     *
     * @return void
     */
    public function visitProperty(PropertyMetadata $metadata, $data): void;

    /**
     * Called after all properties of the object have been visited.
     *
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function endVisitingObject(ClassMetadata $metadata, object $data, TypeDefinition $type);
}
