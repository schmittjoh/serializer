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

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Generic Deserialization Visitor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class GenericDeserializationVisitor extends AbstractVisitor
{
    private $navigator;
    private $result;
    private $objectStack;
    private $currentObject;

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->result = null;
        $this->objectStack = new \SplStack;
    }

    public function getNavigator()
    {
        return $this->navigator;
    }

    public function prepare($data)
    {
        return $this->decode($data);
    }

    public function visitNull($data, array $type, Context $context)
    {
        return null;
    }

    public function visitString($data, array $type, Context $context)
    {
        $data = (string)$data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        $data = (Boolean)$data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitInteger($data, array $type, Context $context)
    {
        $data = (integer)$data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitDouble($data, array $type, Context $context)
    {
        $data = (double)$data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitArray($data, array $type, Context $context)
    {
        if (!is_array($data)) {
            throw new RuntimeException(sprintf('Expected array, but got %s: %s', gettype($data), json_encode($data)));
        }

        // If no further parameters were given, keys/values are just passed as is.
        if (!$type['params']) {
            if (null === $this->result) {
                $this->result = $data;
            }

            return $data;
        }

        switch (count($type['params'])) {
            case 1: // Array is a list.
                $listType = $type['params'][0];

                $result = array();
                if (null === $this->result) {
                    $this->result = &$result;
                }

                foreach ($data as $v) {
                    $result[] = $this->navigator->accept($v, $listType, $context);
                }

                return $result;

            case 2: // Array is a map.
                list($keyType, $entryType) = $type['params'];

                $result = array();
                if (null === $this->result) {
                    $this->result = &$result;
                }

                foreach ($data as $k => $v) {
                    $result[$this->navigator->accept($k, $keyType, $context)] = $this->navigator->accept($v, $entryType, $context);
                }

                return $result;

            default:
                throw new RuntimeException(sprintf('Array type cannot have more than 2 parameters, but got %s.', json_encode($type['params'])));
        }
    }

    public function startVisitingObject(ClassMetadata $metadata, $object, array $type, Context $context)
    {
        $this->setCurrentObject($object);

        if (null === $this->result) {
            $this->result = $this->currentObject;
        }
    }

    public function visitProperty(PropertyMetadata $metadata, $data, Context $context)
    {
        $name = $this->namingStrategy->translateName($metadata);

        if (null === $data) {
            return;
        }

        if (!is_array($data)) {
            throw new RuntimeException(sprintf('Invalid data "%s"(%s), expected "%s".', $data, $metadata->type['name'], $metadata->reflection->class));
        }

        if (!array_key_exists($name, $data)) {
            return;
        }

        if (!$metadata->type) {
            throw new RuntimeException(sprintf('You must define a type for %s::$%s.', $metadata->reflection->class, $metadata->name));
        }

        $v = $data[$name] !== null ? $this->navigator->accept($data[$name], $metadata->type, $context) : null;

        $this->accessor->setValue($this->currentObject, $v, $metadata);

    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        $obj = $this->currentObject;
        $this->revertCurrentObject();

        return $obj;
    }

    public function getResult()
    {
        return $this->result;
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

    abstract protected function decode($str);
}
