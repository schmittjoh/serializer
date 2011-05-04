<?php

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