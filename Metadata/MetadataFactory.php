<?php

namespace JMS\SerializerBundle\Metadata;

use JMS\SerializerBundle\Metadata\Driver\DriverInterface;

class MetadataFactory
{
    private $driver;
    private $loadedMetadata = array();
    private $loadedClassMetadata = array();

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function getMetadataForClass($className)
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }

        $metadata = new ClassHierarchyMetadata();
        foreach ($this->getClassHierarchy($className) as $class) {
            if (!isset($this->loadedClassMetadata[$name = $class->getName()])) {
                $this->loadedClassMetadata[$name] = $this->driver->loadMetadataForClass($class);
            }

            $metadata->addClass($this->loadedClassMetadata[$name]);
        }

        return $this->loadedMetadata[$className] = $metadata;
    }

    private function getClassHierarchy($class)
    {
        $refl = new \ReflectionClass($class);
        $classes = array();
        do {
            $classes[] = $refl;
        } while (false !== $refl = $refl->getParentClass());

        return array_reverse($classes, false);
    }
}