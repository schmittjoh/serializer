<?php

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