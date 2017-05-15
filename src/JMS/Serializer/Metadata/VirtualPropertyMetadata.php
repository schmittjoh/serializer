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

class VirtualPropertyMetadata extends PropertyMetadata
{
    public function __construct($class, $methodName)
    {
        if (0 === strpos($methodName, 'get')) {
            $fieldName = lcfirst(substr($methodName, 3));
        } else {
            $fieldName = $methodName;
        }

        $this->class = $class;
        $this->name = $fieldName;
        $this->getter = $methodName;
        $this->readOnly = true;
    }

    public function setValue($obj, $value)
    {
        throw new \LogicException('VirtualPropertyMetadata is immutable.');
    }

    public function setAccessor($type, $getter = null, $setter = null)
    {
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
            $this->xmlAttributeMap,
            $this->maxDepth,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->class,
            $this->name,
            'excludeIf' => $this->excludeIf,
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
            $this->xmlAttributeMap,
            $this->maxDepth,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->class,
            $this->name
            ) = $unserialized;

        if (isset($unserialized['excludeIf'])) {
            $this->excludeIf = $unserialized['excludeIf'];
        }
    }
}
