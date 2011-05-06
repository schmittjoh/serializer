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

class PropertyMetadata implements \Serializable
{
    public $class;
    public $name;
    public $reflection;
    public $sinceVersion;
    public $untilVersion;
    public $serializedName;
    public $exposed;
    public $excluded;
    public $type;

    public function __construct($class, $name)
    {
        $this->class    = $class;
        $this->name     = $name;
        $this->exposed  = false;
        $this->excluded = false;

        $this->reflection = new \ReflectionProperty($class, $name);
        $this->reflection->setAccessible(true);
    }

    public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->name,
            $this->sinceVersion,
            $this->untilVersion,
            $this->serializedName,
            $this->exposed,
            $this->excluded,
            $this->type,
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->class,
            $this->name,
            $this->sinceVersion,
            $this->untilVersion,
            $this->serializedName,
            $this->exposed,
            $this->excluded,
            $this->type
        ) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}