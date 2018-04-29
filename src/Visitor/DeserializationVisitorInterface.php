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
interface DeserializationVisitorInterface extends VisitorInterface
{
    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitNull($data, TypeDefinition $type): void;

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitString($data, TypeDefinition $type): string;

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitBoolean($data, TypeDefinition $type): bool;

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitDouble($data, TypeDefinition $type): float;

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitInteger($data, TypeDefinition $type): int;

    /**
     * Returns the class name based on the type of the discriminator map value
     *
     * @param $data
     * @param ClassMetadata $metadata
     * @return string
     */
    public function visitDiscriminatorMapProperty($data, ClassMetadata $metadata): string;

    /**
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function visitArray($data, TypeDefinition $type): array;

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
     * @return mixed
     */
    public function visitProperty(PropertyMetadata $metadata, $data);

    /**
     * Called after all properties of the object have been visited.
     *
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param TypeDefinition $type
     *
     * @return mixed
     */
    public function endVisitingObject(ClassMetadata $metadata, $data, TypeDefinition $type): object;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function getResult($data);
}
