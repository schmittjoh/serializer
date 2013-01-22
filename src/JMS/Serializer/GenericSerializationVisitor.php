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

namespace JMS\Serializer;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Metadata\PropertyMetadata;

abstract class GenericSerializationVisitor extends AbstractVisitor
{
    private $navigator;
    private $root;
    private $dataStack;
    private $data;

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->root = null;
        $this->dataStack = new \SplStack;
    }

    public function getNavigator()
    {
        return $this->navigator;
    }

    public function visitNull($data, array $type)
    {
        return null;
    }

    public function visitString($data, array $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (string) $data;
    }

    public function visitBoolean($data, array $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (boolean) $data;
    }

    public function visitInteger($data, array $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (int) $data;
    }

    public function visitDouble($data, array $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (float) $data;
    }

    /**
     * @param array $data
     * @param array $type
     */
    public function visitArray($data, array $type)
    {
        if (null === $this->root) {
            $this->root = array();
            $rs = &$this->root;
        } else {
            // ArrayObject is specially treated by the json_encode function and
            // serialized to { } while a mere array would be serialized to [].
            $rs = isset($type['params'][1]) ? new \ArrayObject() : array();
        }

        foreach ($data as $k => $v) {
            $v = $this->navigator->accept($v, isset($type['params'][1]) ? $type['params'][1] : null, $this);

            if (null === $v && (!is_string($k) || !$this->shouldSerializeNull())) {
                continue;
            }

            $rs[$k] = $v;
        }

        return $rs;
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, array $type)
    {
        if (null === $this->root) {
            $this->root = new \stdClass;
        }

        $this->dataStack->push($this->data);
        $this->data = array();
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type)
    {
        $rs = $this->data;
        $this->data = $this->dataStack->pop();

        if ($this->root instanceof \stdClass && 0 === $this->dataStack->count()) {
            $this->root = $rs;
        }

        return $rs;
    }

    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $v = (null === $metadata->getter ? $metadata->reflection->getValue($data)
                : $data->{$metadata->getter}());

        $v = $this->navigator->accept($v, $metadata->type, $this);
        if (null === $v && !$this->shouldSerializeNull()) {
            return;
        }

        $k = $this->namingStrategy->translateName($metadata);

        if ($metadata->inline && is_array($v)) {
            $this->data = array_merge($this->data, $v);
        } else {
            $this->data[$k] = $v;
        }
    }

    /**
     * Allows you to add additional data to the current object/root element.
     *
     * @param string $key
     * @param scalar|array $value This value must either be a regular scalar, or an array.
     *                            It must not contain any objects anymore.
     */
    public function addData($key, $value)
    {
        if (isset($this->data[$key])) {
            throw new InvalidArgumentException(sprintf('There is already data for "%s".', $key));
        }

        $this->data[$key] = $value;
    }

    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param array $data
     */
    public function setRoot($data)
    {
        $this->root = $data;
    }
}
