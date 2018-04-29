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

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Type\TypeDefinition;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

final class JsonDeserializationVisitor extends AbstractVisitor implements DeserializationVisitorInterface
{
    private $options = 0;
    private $depth = 512;

    private $objectStack;
    private $currentObject;

    public function __construct(
        int $options = 0, int $depth = 512)
    {
        $this->objectStack = new \SplStack;
        $this->options = $options;
        $this->depth = $depth;
    }

    public function visitNull($data, TypeDefinition $type): void
    {

    }

    public function visitString($data, TypeDefinition $type): string
    {
        return (string)$data;
    }

    public function visitBoolean($data, TypeDefinition $type): bool
    {
        return (bool)$data;
    }

    public function visitInteger($data, TypeDefinition $type): int
    {
        return (int)$data;
    }

    public function visitDouble($data, TypeDefinition $type): float
    {
        return (double)$data;
    }

    public function visitArray($data, TypeDefinition $type): array
    {
        if (!\is_array($data)) {
            throw new RuntimeException(sprintf('Expected array, but got %s: %s', \gettype($data), json_encode($data)));
        }

        // If no further parameters were given, keys/values are just passed as is.
        if (!$type['params']) {
            return $data;
        }

        switch (\count($type['params'])) {
            case 1: // Array is a list.
                $listType = $type['params'][0];

                $result = [];

                foreach ($data as $v) {
                    $result[] = $this->navigator->accept($v, $listType);
                }

                return $result;

            case 2: // Array is a map.
                list($keyType, $entryType) = $type['params'];

                $result = [];

                foreach ($data as $k => $v) {
                    $result[$this->navigator->accept($k, $keyType)] = $this->navigator->accept($v, $entryType);
                }

                return $result;

            default:
                throw new RuntimeException(sprintf('Array type cannot have more than 2 parameters, but got %s.', json_encode($type['params'])));
        }
    }

    public function visitDiscriminatorMapProperty($data, ClassMetadata $metadata): string
    {
        if (isset($data[$metadata->discriminatorFieldName])) {
            return (string)$data[$metadata->discriminatorFieldName];
        }

        throw new LogicException(sprintf(
            'The discriminator field name "%s" for base-class "%s" was not found in input data.',
            $metadata->discriminatorFieldName,
            $metadata->name
        ));
    }

    public function startVisitingObject(ClassMetadata $metadata, object $object, TypeDefinition $type): void
    {
        $this->setCurrentObject($object);
    }

    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $name = $metadata->serializedName;

        if (null === $data) {
            return;
        }

        if (!\is_array($data)) {
            throw new RuntimeException(sprintf('Invalid data "%s"(%s), expected "%s".', $data, $metadata->type['name'], $metadata->reflection->class));
        }

        if (!array_key_exists($name, $data)) {
            return;
        }

        if (!$metadata->type) {
            throw new RuntimeException(sprintf('You must define a type for %s::$%s.', $metadata->reflection->class, $metadata->name));
        }

        $v = $data[$name] !== null ? $this->navigator->accept($data[$name], $metadata->type) : null;

        return $v;
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, TypeDefinition $type): object
    {
        $obj = $this->currentObject;
        $this->revertCurrentObject();

        return $obj;
    }

    public function getResult($data)
    {
        return $data;
    }

    public function setCurrentObject($object)
    {
        $this->objectStack->push($this->currentObject);
        $this->currentObject = $object;
    }

    public function getCurrentObject()
    {
        return $this->currentObject;
    }

    public function revertCurrentObject()
    {
        return $this->currentObject = $this->objectStack->pop();
    }

    public function prepare($str)
    {
        $decoded = json_decode($str, true, $this->depth, $this->options);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $decoded;

            case JSON_ERROR_DEPTH:
                throw new RuntimeException('Could not decode JSON, maximum stack depth exceeded.');

            case JSON_ERROR_STATE_MISMATCH:
                throw new RuntimeException('Could not decode JSON, underflow or the nodes mismatch.');

            case JSON_ERROR_CTRL_CHAR:
                throw new RuntimeException('Could not decode JSON, unexpected control character found.');

            case JSON_ERROR_SYNTAX:
                throw new RuntimeException('Could not decode JSON, syntax error - malformed JSON.');

            case JSON_ERROR_UTF8:
                throw new RuntimeException('Could not decode JSON, malformed UTF-8 characters (incorrectly encoded?)');

            default:
                throw new RuntimeException('Could not decode JSON.');
        }
    }
}
