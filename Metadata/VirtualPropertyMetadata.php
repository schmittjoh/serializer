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


class VirtualPropertyMetadata extends PropertyMetadata
{

    public function __construct($class, $methodName)
    {
        if ('get' === substr($methodName, 0, 3)) {
            $fieldName = lcfirst(substr($methodName, 3));
        } else {
            $fieldName = $methodName;
        }

        $this->class = $class;
        $this->name = $fieldName;
        $this->getter = $methodName;
        $this->readOnly = true;
    }

    public function getValue($obj)
    {
        return $obj->{$this->getter}();
    }

    public function setValue($obj, $value)
    {
        return false;
    }

    public function setAccessor($type, $getter = null, $setter = null)
    {
        return false;
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
            $this->xmlKeyValuePairs,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->class,
            $this->name
        ));
    }

    public function unserialize($str)
    {
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
            $this->xmlKeyValuePairs,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->class,
            $this->name
        ) = unserialize($str);
    }
}
