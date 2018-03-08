<?php

namespace JMS\Serializer\Accessor\Finder;

/**
 * Finds getter and setter for class property.
 * @see \JMS\Serializer\Annotation\AccessorFinder
 */
interface AccessorFinderInterface
{
    /**
     * @param \ReflectionClass $class
     * @param string $propertyName
     * @return string|null
     */
    public function findGetter(\ReflectionClass $class, $propertyName);

    /**
     * @param \ReflectionClass $class
     * @param string $propertyName
     * @return string|null
     */
    public function findSetter(\ReflectionClass $class, $propertyName);
}
