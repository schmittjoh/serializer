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

use JMS\SerializerBundle\Exception\RuntimeException;
use JMS\SerializerBundle\Serializer\Construction\ObjectConstructorInterface;
use JMS\SerializerBundle\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\ClassMetadata;

/**
 * Generic Deserialization Visitor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class GenericDeserializationVisitor extends AbstractDeserializationVisitor
{
    private $navigator;
    private $result;
    private $objectConstructor;
    private $objectStack;
    private $currentObject;

    public function __construct(PropertyNamingStrategyInterface $namingStrategy, array $customHandlers, ObjectConstructorInterface $objectConstructor)
    {
        parent::__construct($namingStrategy, $customHandlers);
        $this->objectConstructor = $objectConstructor;
    }

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

    public function visitString($data, $type)
    {
        $data = (string) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitBoolean($data, $type)
    {
        $data = (Boolean) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitInteger($data, $type)
    {
        $data = (integer) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitDouble($data, $type)
    {
        $data = (double) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitArray($data, $type)
    {
        if (!is_array($data)) {
            throw new RuntimeException(sprintf('Expected array, but got %s: %s', gettype($data), json_encode($data)));
        }

        // not specified of which type keys/values should be, just pass as is
        if ('array' === $type) {
            if (null === $this->result) {
                $this->result = $data;
            }

            return $data;
        }

        // list
        if (false === $pos = strpos($type, ',', 6)) {
            $listType = substr($type, 6, -1);

            $result = array();
            if (null === $this->result) {
                $this->result = &$result;
            }

            foreach ($data as $v) {
                $result[] = $this->navigator->accept($v, $listType, $this);
            }

            return $result;
        }

        // map
        $keyType = trim(substr($type, 6, $pos - 6));
        $entryType = trim(substr($type, $pos+1, -1));

        $result = array();
        if (null === $this->result) {
            $this->result = &$result;
        }

        foreach ($data as $k => $v) {
            $result[$this->navigator->accept($k, $keyType, $this)] = $this->navigator->accept($v, $entryType, $this);
        }

        return $result;
    }

    public function visitTraversable($data, $type)
    {
        throw new RuntimeException('Traversable is not supported for deserialization.');
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, $type)
    {
        $this->setCurrentObject($this->objectConstructor->construct($this, $metadata, $data, $type));

        if (null === $this->result) {
            $this->result = $this->currentObject;
        }
    }

    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $name = $this->namingStrategy->translateName($metadata);

        if (!isset($data[$name])) {
            return;
        }

        if (!$metadata->type) {
            throw new RuntimeException(sprintf('You must define a type for %s::$%s.', $metadata->reflection->getDeclaringClass()->getName(), $metadata->name));
        }

        $v = $this->navigator->accept($data[$name], $metadata->type, $this);
        if (null === $v) {
            return;
        }

        if (null === $metadata->setter) {
            $metadata->reflection->setValue($this->currentObject, $v);

            return;
        }

        $this->currentObject->{$metadata->setter}($v);
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, $type)
    {
        $obj = $this->currentObject;
        $this->revertCurrentObject();

        return $obj;
    }

    public function visitPropertyUsingCustomHandler(PropertyMetadata $metadata, $object)
    {
        // TODO
        return false;
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
