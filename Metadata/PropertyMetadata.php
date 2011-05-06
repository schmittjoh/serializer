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
    private $class;
    private $name;
    private $reflection;
    private $sinceVersion;
    private $untilVersion;
    private $serializedName;
    private $exposed;
    private $excluded;
    private $type;

    public function __construct($class, $name)
    {
        $this->class    = $class;
        $this->name     = $name;
        $this->exposed  = false;
        $this->excluded = false;

        $this->reflection = new \ReflectionProperty($class, $name);
        $this->reflection->setAccessible(true);
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function setSinceVersion($version)
    {
        $this->sinceVersion = $version;
    }

    public function getSinceVersion()
    {
        return $this->sinceVersion;
    }

    public function setUntilVersion($version)
    {
        $this->untilVersion = $version;
    }

    public function getUntilVersion()
    {
        return $this->untilVersion;
    }

    public function setSerializedName($name)
    {
        $this->serializedName = $name;
    }

    public function getSerializedName()
    {
        return $this->serializedName;
    }

    public function setExposed($bool)
    {
        $this->exposed = (Boolean) $bool;
    }

    public function isExposed()
    {
        return $this->exposed;
    }

    public function setExcluded($bool)
    {
        $this->excluded = (Boolean) $bool;
    }

    public function isExcluded()
    {
        return $this->excluded;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
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