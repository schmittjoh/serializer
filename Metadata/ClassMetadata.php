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

namespace JMS\SerializerBundle\Metadata;

use JMS\SerializerBundle\Exception\InvalidArgumentException;
use Metadata\MergeableInterface;
use Metadata\MethodMetadata;
use Metadata\MergeableClassMetadata;
use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * Class Metadata used to customize the serialization process.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ClassMetadata extends MergeableClassMetadata
{
    const ACCESSOR_ORDER_UNDEFINED = 'undefined';
    const ACCESSOR_ORDER_ALPHABETICAL = 'alphabetical';
    const ACCESSOR_ORDER_CUSTOM = 'custom';

    public $preSerializeMethods = array();
    public $postSerializeMethods = array();
    public $postDeserializeMethods = array();
    public $xmlRootName;
    public $accessorOrder;
    public $customOrder;

    /**
     * Sets the order of properties in the class.
     *
     * @param string $order
     * @param array $customOrder
     */
    public function setAccessorOrder($order, array $customOrder = array())
    {
        if (!in_array($order, array(self::ACCESSOR_ORDER_UNDEFINED, self::ACCESSOR_ORDER_ALPHABETICAL, self::ACCESSOR_ORDER_CUSTOM), true)) {
            throw new \InvalidArgumentException(sprintf('The accessor order "%s" is invalid.', $order));
        }

        foreach ($customOrder as $name) {
            if (!is_string($name)) {
                throw new \InvalidArgumentException(sprintf('$customOrder is expected to be a list of strings, but got element of value %s.', json_encode($name)));
            }
        }

        $this->accessorOrder = $order;
        $this->customOrder = array_flip($customOrder);
        $this->sortProperties();
    }

    public function addPropertyMetadata(BasePropertyMetadata $metadata)
    {
        parent::addPropertyMetadata($metadata);
        $this->sortProperties();
    }

    public function addPreSerializeMethod(MethodMetadata $method)
    {
        $this->preSerializeMethods[] = $method;
    }

    public function addPostSerializeMethod(MethodMetadata $method)
    {
        $this->postSerializeMethods[] = $method;
    }

    public function addPostDeserializeMethod(MethodMetadata $method)
    {
        $this->postDeserializeMethods[] = $method;
    }

    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof ClassMetadata) {
            throw new InvalidArgumentException('$object must be an instance of ClassMetadata.');
        }
        parent::merge($object);

        $this->preSerializeMethods = array_merge($this->preSerializeMethods, $object->preSerializeMethods);
        $this->postSerializeMethods = array_merge($this->postSerializeMethods, $object->postSerializeMethods);
        $this->postDeserializeMethods = array_merge($this->postDeserializeMethods, $object->postDeserializeMethods);
        $this->xmlRootName = $object->xmlRootName;

        if ($object->accessorOrder) {
            $this->accessorOrder = $object->accessorOrder;
            $this->customOrder = $object->customOrder;
        }

        $this->sortProperties();
    }

    public function serialize()
    {
        $this->sortProperties();

        return serialize(array(
            $this->preSerializeMethods,
            $this->postSerializeMethods,
            $this->postDeserializeMethods,
            $this->xmlRootName,
            $this->accessorOrder,
            $this->customOrder,
            parent::serialize(),
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->preSerializeMethods,
            $this->postSerializeMethods,
            $this->postDeserializeMethods,
            $this->xmlRootName,
            $this->accessorOrder,
            $this->customOrder,
            $parentStr
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }

    private function sortProperties()
    {
        switch ($this->accessorOrder) {
            case self::ACCESSOR_ORDER_ALPHABETICAL:
                ksort($this->propertyMetadata);
                break;

            case self::ACCESSOR_ORDER_CUSTOM:
                $order = $this->customOrder;
                uksort($this->propertyMetadata, function($a, $b) use ($order) {
                    $existsA = isset($order[$a]);
                    $existsB = isset($order[$b]);

                    if (!$existsA && !$existsB) {
                        return 0;
                    }

                    if (!$existsA) {
                        return 1;
                    }

                    if (!$existsB) {
                        return -1;
                    }

                    return $order[$a] < $order[$b] ? -1 : 1;
                });
                break;
        }
    }
}