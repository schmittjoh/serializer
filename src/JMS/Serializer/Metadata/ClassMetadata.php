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

namespace JMS\Serializer\Metadata;

use JMS\Serializer\Exception\InvalidArgumentException;
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

    /** @var \ReflectionMethod[] */
    public $preSerializeMethods = array();

    /** @var \ReflectionMethod[] */
    public $postSerializeMethods = array();

    /** @var \ReflectionMethod[] */
    public $postDeserializeMethods = array();

    public $xmlRootName;
    public $xmlRootNamespace;
    public $xmlNamespaces = array();
    public $accessorOrder;
    public $customOrder;
    public $handlerCallbacks = array();

    public $discriminatorDisabled = false;
    public $discriminatorBaseClass;
    public $discriminatorFieldName;
    public $discriminatorValue;
    public $discriminatorMap = array();

    public function setDiscriminator($fieldName, array $map)
    {
        if (empty($fieldName)) {
            throw new \InvalidArgumentException('The $fieldName cannot be empty.');
        }

        if (empty($map)) {
            throw new \InvalidArgumentException('The discriminator map cannot be empty.');
        }

        $this->discriminatorBaseClass = $this->name;
        $this->discriminatorFieldName = $fieldName;
        $this->discriminatorMap = $map;
    }

    /**
     * Sets the order of properties in the class.
     *
     * @param string $order
     * @param array $customOrder
     *
     * @throws InvalidArgumentException When the accessor order is not valid
     * @throws InvalidArgumentException When the custom order is not valid
     */
    public function setAccessorOrder($order, array $customOrder = array())
    {
        if ( ! in_array($order, array(self::ACCESSOR_ORDER_UNDEFINED, self::ACCESSOR_ORDER_ALPHABETICAL, self::ACCESSOR_ORDER_CUSTOM), true)) {
            throw new InvalidArgumentException(sprintf('The accessor order "%s" is invalid.', $order));
        }

        foreach ($customOrder as $name) {
            if ( ! is_string($name)) {
                throw new InvalidArgumentException(sprintf('$customOrder is expected to be a list of strings, but got element of value %s.', json_encode($name)));
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

    /**
     * @param integer $direction
     * @param string|integer $format
     * @param string $methodName
     */
    public function addHandlerCallback($direction, $format, $methodName)
    {
        $this->handlerCallbacks[$direction][$format] = $methodName;
    }

    public function merge(MergeableInterface $object)
    {
        if ( ! $object instanceof ClassMetadata) {
            throw new InvalidArgumentException('$object must be an instance of ClassMetadata.');
        }
        parent::merge($object);

        $this->preSerializeMethods = array_merge($this->preSerializeMethods, $object->preSerializeMethods);
        $this->postSerializeMethods = array_merge($this->postSerializeMethods, $object->postSerializeMethods);
        $this->postDeserializeMethods = array_merge($this->postDeserializeMethods, $object->postDeserializeMethods);
        $this->xmlRootName = $object->xmlRootName;
        $this->xmlRootNamespace = $object->xmlRootNamespace;
        $this->xmlNamespaces = array_merge($this->xmlNamespaces, $object->xmlNamespaces);

        // Handler methods are taken from the outer class completely.
        $this->handlerCallbacks = $object->handlerCallbacks;

        if ($object->accessorOrder) {
            $this->accessorOrder = $object->accessorOrder;
            $this->customOrder = $object->customOrder;
        }

        if ($object->discriminatorFieldName && $this->discriminatorFieldName) {
            throw new \LogicException(sprintf(
                'The discriminator of class "%s" would overwrite the discriminator of the parent class "%s". Please define all possible sub-classes in the discriminator of %s.',
                $object->name,
                $this->discriminatorBaseClass,
                $this->discriminatorBaseClass
            ));
        } elseif ( ! $this->discriminatorFieldName && $object->discriminatorFieldName) {
            $this->discriminatorFieldName = $object->discriminatorFieldName;
            $this->discriminatorMap = $object->discriminatorMap;
        }

        if ($object->discriminatorDisabled !== null) {
            $this->discriminatorDisabled = $object->discriminatorDisabled;
        }

        if ($object->discriminatorMap) {
            $this->discriminatorFieldName = $object->discriminatorFieldName;
            $this->discriminatorMap = $object->discriminatorMap;
            $this->discriminatorBaseClass = $object->discriminatorBaseClass;
        }

        if ($this->discriminatorMap && ! $this->reflection->isAbstract()) {
            if (false === $typeValue = array_search($this->name, $this->discriminatorMap, true)) {
                throw new \LogicException(sprintf(
                    'The sub-class "%s" is not listed in the discriminator of the base class "%s".',
                    $this->name,
                    $this->discriminatorBaseClass
                ));
            }

            $this->discriminatorValue = $typeValue;

            if (isset($this->propertyMetadata[$this->discriminatorFieldName])
                    && ! $this->propertyMetadata[$this->discriminatorFieldName] instanceof StaticPropertyMetadata) {
                throw new \LogicException(sprintf(
                    'The discriminator field name "%s" of the base-class "%s" conflicts with a regular property of the sub-class "%s".',
                    $this->discriminatorFieldName,
                    $this->discriminatorBaseClass,
                    $this->name
                ));
            }

            $discriminatorProperty = new StaticPropertyMetadata(
                $this->name,
                $this->discriminatorFieldName,
                $typeValue
            );
            $discriminatorProperty->serializedName = $this->discriminatorFieldName;
            $this->propertyMetadata[$this->discriminatorFieldName] = $discriminatorProperty;
        }

        $this->sortProperties();
    }

    public function registerNamespace($uri, $prefix = null)
    {
        if ( ! is_string($uri)) {
            throw new InvalidArgumentException(sprintf('$uri is expected to be a strings, but got value %s.', json_encode($uri)));
        }

        if ($prefix !== null) {
            if ( ! is_string($prefix)) {
                throw new InvalidArgumentException(sprintf('$prefix is expected to be a strings, but got value %s.', json_encode($prefix)));
            }
        } else {
            $prefix = "";
        }

        $this->xmlNamespaces[$prefix] = $uri;

    }

    public function serialize()
    {
        $this->sortProperties();

        return serialize(array(
            $this->preSerializeMethods,
            $this->postSerializeMethods,
            $this->postDeserializeMethods,
            $this->xmlRootName,
            $this->xmlRootNamespace,
            $this->xmlNamespaces,
            $this->accessorOrder,
            $this->customOrder,
            $this->handlerCallbacks,
            $this->discriminatorDisabled,
            $this->discriminatorBaseClass,
            $this->discriminatorFieldName,
            $this->discriminatorValue,
            $this->discriminatorMap,
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
            $this->xmlRootNamespace,
            $this->xmlNamespaces,
            $this->accessorOrder,
            $this->customOrder,
            $this->handlerCallbacks,
            $this->discriminatorDisabled,
            $this->discriminatorBaseClass,
            $this->discriminatorFieldName,
            $this->discriminatorValue,
            $this->discriminatorMap,
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
                $currentSorting = $this->propertyMetadata ? array_combine(array_keys($this->propertyMetadata), range(1, count($this->propertyMetadata))) : [];
                uksort($this->propertyMetadata, function($a, $b) use ($order, $currentSorting) {
                    $existsA = isset($order[$a]);
                    $existsB = isset($order[$b]);

                    if ( ! $existsA && ! $existsB) {
                        return $currentSorting[$a] - $currentSorting[$b];
                    }

                    if ( ! $existsA) {
                        return 1;
                    }

                    if ( ! $existsB) {
                        return -1;
                    }

                    return $order[$a] < $order[$b] ? -1 : 1;
                });
                break;
        }
    }
}
