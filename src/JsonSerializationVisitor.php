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
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Type\TypeDefinition;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class JsonSerializationVisitor extends AbstractVisitor implements SerializationVisitorInterface
{
    private $options = JSON_PRESERVE_ZERO_FRACTION;

    private $dataStack;
    private $data;

    public function __construct(
        int $options = JSON_PRESERVE_ZERO_FRACTION)
    {
        $this->dataStack = new \SplStack;
        $this->options = $options;
    }

    public function visitNull($data, TypeDefinition $type)
    {
        return null;
    }

    public function visitString(string $data, TypeDefinition $type)
    {
        return $data;
    }

    public function visitBoolean(bool $data, TypeDefinition $type)
    {
        return $data;
    }

    public function visitInteger(int $data, TypeDefinition $type)
    {
        return $data;
    }

    public function visitDouble(float $data, TypeDefinition $type)
    {
        return $data;
    }

    /**
     * @param array $data
     * @param TypeDefinition $type
     * @return mixed
     */
    public function visitArray(array $data, TypeDefinition $type)
    {
        $this->dataStack->push($data);

        $rs = $type->hasParam('1') ? new \ArrayObject() : [];

        $isList = $type->hasParam('0') && !$type->hasParam('1');

        $elType = $this->getElementType($type);
        foreach ($data as $k => $v) {

            try {
                $v = $this->navigator->accept($v, $elType);
            } catch (NotAcceptableException $e) {
                continue;
            }

            if ($isList) {
                $rs[] = $v;
            } else {
                $rs[$k] = $v;
            }
        }

        $this->dataStack->pop();
        return $rs;
    }

    public function startVisitingObject(ClassMetadata $metadata, object $data, TypeDefinition $type): void
    {
        $this->dataStack->push($this->data);
        $this->data = [];
    }

    public function endVisitingObject(ClassMetadata $metadata, object $data, TypeDefinition $type)
    {
        $rs = $this->data;
        $this->data = $this->dataStack->pop();

        // Force JSON output to "{}" instead of "[]" if it contains either no properties or all properties are null.
        if (empty($rs)) {
            $rs = new \ArrayObject();
        }

        return $rs;
    }

    public function visitProperty(PropertyMetadata $metadata, $v): void
    {
        try {
            $v = $this->navigator->accept($v, $metadata->type);
        } catch (NotAcceptableException $e) {
            return;
        }

        if (true === $metadata->skipWhenEmpty && ($v instanceof \ArrayObject || \is_array($v)) && 0 === count($v)) {
            return;
        }

        if ($metadata->inline) {
            if (\is_array($v) || ($v instanceof \ArrayObject)) {
                $this->data = array_merge($this->data, (array)$v);
            }
        } else {
            $this->data[$metadata->serializedName] = $v;
        }
    }

    /**
     * Checks if some data key exists.
     *
     * @param string $key
     * @return boolean
     */
    public function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Allows you to replace existing data on the current object/root element.
     *
     * @param string $key
     * @param mixed $value This value must either be a regular scalar, or an array.
     *                                                       It must not contain any objects anymore.
     */
    public function setData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function getResult($data)
    {
        $result = @json_encode($data, $this->options);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $result;

            case JSON_ERROR_UTF8:
                throw new RuntimeException('Your data could not be encoded because it contains invalid UTF8 characters.');

            default:
                throw new RuntimeException(sprintf('An error occurred while encoding your data (error code %d).', json_last_error()));
        }
    }
}
