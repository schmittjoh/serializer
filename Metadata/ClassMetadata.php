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

class ClassMetadata implements \Serializable
{
    private $name;
    private $reflection;
    private $properties = array();
    private $exclusionPolicy = 'NONE';

    public function __construct($name)
    {
        $this->name = $name;
        $this->reflection = new \ReflectionClass($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function addPropertyMetadata(PropertyMetadata $metadata)
    {
        $this->properties[$metadata->getName()] = $metadata;
    }

    public function getExclusionPolicy()
    {
        return $this->exclusionPolicy;
    }

    public function setExclusionPolicy($policy)
    {
        $this->exclusionPolicy = $policy;
    }

    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->properties,
            $this->exclusionPolicy,
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->properties,
            $this->exclusionPolicy
        ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}