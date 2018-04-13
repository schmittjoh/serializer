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

use JMS\Serializer\TypeParser;
use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    const ACCESS_TYPE_PROPERTY = 'property';
    const ACCESS_TYPE_PUBLIC_METHOD = 'public_method';

    const ACCESS_TYPE_NAMING_EXACT = 'exact';
    const ACCESS_TYPE_NAMING_CAMEL_CASE = 'camel_case';

    public $sinceVersion;
    public $untilVersion;
    public $groups;
    public $serializedName;
    public $type;
    public $xmlCollection = false;
    public $xmlCollectionInline = false;
    public $xmlCollectionSkipWhenEmpty = true;
    public $xmlEntryName;
    public $xmlEntryNamespace;
    public $xmlKeyAttribute;
    public $xmlAttribute = false;
    public $xmlValue = false;
    public $xmlNamespace;
    public $xmlKeyValuePairs = false;
    public $xmlElementCData = true;
    public $getter;
    public $setter;
    public $inline = false;
    public $skipWhenEmpty = false;
    public $readOnly = false;
    public $xmlAttributeMap = false;
    public $maxDepth = null;
    public $excludeIf = null;

    /** @deprecated Use getReflection() */
    public $reflection;
    public $accessType;
    public $accessTypeNaming;

    private $closureAccessor;

    /**
     * @var \ReflectionProperty|null
     */
    private $lazyReflection;

    private static $typeParser;

    /**
     * @param string $class
     * @param string $name
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name = $name;
        $this->prepareLazyReflection();

        $this->closureAccessor = \Closure::bind(function ($o, $name) {
            return $o->$name;
        }, null, $class);
    }

    public function __get($name)
    {
        // Default PHP notice when no property:
        if ($name !== 'reflection') {
            trigger_error("Undefined property: $name", \E_NOTICE);
            return null;
        }

        return $this->getReflection();
    }

    public function getReflection()
    {
        if (!$this->lazyReflection) {
            $this->lazyReflection = new \ReflectionProperty($this->class, $this->name);
            $this->lazyReflection->setAccessible(true);
        }

        return $this->lazyReflection;
    }

    /**
     * @see PropertyMetadata::ACCESS_TYPE_PROPERTY
     * @param string $type
     *
     * @param string|null $getter
     * @param string|null $setter
     * @param string $naming
     */
    public function setAccessor($type, $getter = null, $setter = null, $naming = self::ACCESS_TYPE_NAMING_EXACT)
    {
        $this->accessType = $type;
        $this->accessTypeNaming = $naming;
        $this->getter = $getter;
        $this->setter = $setter;
    }

    public function getValue($obj)
    {
        if (null === $this->getter) {
            if (null !== $this->closureAccessor) {
                $accessor = $this->closureAccessor;
                return $accessor($obj, $this->name);
            }

            return parent::getValue($obj);
        }

        return $obj->{$this->getter}();
    }

    public function setValue($obj, $value)
    {
        if (null === $this->setter) {
            parent::setValue($obj, $value);
            return;
        }

        $obj->{$this->setter}($value);
    }

    public function setType($type)
    {
        if (null === self::$typeParser) {
            self::$typeParser = new TypeParser();
        }

        $this->type = self::$typeParser->parse($type);
    }

    public function serialize()
    {
        return serialize(array(
            $this->sinceVersion,
            $this->untilVersion,
            $this->groups,
            $this->serializedName,
            $this->type,
            $this->xmlCollection,
            $this->xmlCollectionInline,
            $this->xmlEntryName,
            $this->xmlKeyAttribute,
            $this->xmlAttribute,
            $this->xmlValue,
            $this->xmlNamespace,
            $this->xmlKeyValuePairs,
            $this->xmlElementCData,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->xmlAttributeMap,
            $this->maxDepth,
            parent::serialize(),
            'xmlEntryNamespace' => $this->xmlEntryNamespace,
            'xmlCollectionSkipWhenEmpty' => $this->xmlCollectionSkipWhenEmpty,
            'excludeIf' => $this->excludeIf,
            'skipWhenEmpty' => $this->skipWhenEmpty,
        ));
    }

    public function unserialize($str)
    {
        $unserialized = unserialize($str);
        list(
            $this->sinceVersion,
            $this->untilVersion,
            $this->groups,
            $this->serializedName,
            $this->type,
            $this->xmlCollection,
            $this->xmlCollectionInline,
            $this->xmlEntryName,
            $this->xmlKeyAttribute,
            $this->xmlAttribute,
            $this->xmlValue,
            $this->xmlNamespace,
            $this->xmlKeyValuePairs,
            $this->xmlElementCData,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->xmlAttributeMap,
            $this->maxDepth,
            $parentStr
            ) = $unserialized;

        if (isset($unserialized['xmlEntryNamespace'])) {
            $this->xmlEntryNamespace = $unserialized['xmlEntryNamespace'];
        }
        if (isset($unserialized['xmlCollectionSkipWhenEmpty'])) {
            $this->xmlCollectionSkipWhenEmpty = $unserialized['xmlCollectionSkipWhenEmpty'];
        }
        if (isset($unserialized['excludeIf'])) {
            $this->excludeIf = $unserialized['excludeIf'];
        }
        if (isset($unserialized['skipWhenEmpty'])) {
            $this->skipWhenEmpty = $unserialized['skipWhenEmpty'];
        }

        list($this->class, $this->name) = unserialize($parentStr);
        $this->prepareLazyReflection();

        $this->closureAccessor = \Closure::bind(function ($o, $name) {
            return $o->$name;
        }, null, $this->class);
    }

    protected function prepareLazyReflection()
    {
        // Use __get() instead of $this->reflection.
        unset($this->reflection);
    }
}
