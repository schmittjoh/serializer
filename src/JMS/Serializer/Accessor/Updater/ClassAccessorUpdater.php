<?php

namespace JMS\Serializer\Accessor\Updater;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\ClassMetadataUpdaterInterface;
use JMS\Serializer\Metadata\PropertyMetadata;

class ClassAccessorUpdater implements ClassMetadataUpdaterInterface
{
    /**
     * @var string
     */
    private $defaultType;

    /**
     * @var string
     */
    private $defaultNaming;

    /**
     * @var array
     */
    private $getterPrefixes;

    /**
     * @var array
     */
    private $setterPrefixes;

    public function __construct(
        $defaultType = PropertyMetadata::ACCESS_TYPE_PROPERTY,
        $defaultNaming = PropertyMetadata::ACCESS_TYPE_NAMING_EXACT,
        array $getterPrefixes = ['get', 'is', 'has'],
        array $setterPrefixes = ['set', 'update']
    )
    {
        $this->defaultType = $defaultType;
        $this->defaultNaming = $defaultNaming;
        $this->getterPrefixes = $getterPrefixes;
        $this->setterPrefixes = $setterPrefixes;
    }

    /**
     * {@inheritDoc}
     */
    public function update(ClassMetadata $classMetadata)
    {
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            $this->updateAccessors($propertyMetadata, $classMetadata);
        }
    }

    /**
     * @param string $accessorPrefix
     * @param string $propertyName
     * @param string $naming
     *
     * @return string|null
     */
    protected function getAccessorName($accessorPrefix, $propertyName, $naming)
    {
        switch ($naming) {
            case PropertyMetadata::ACCESS_TYPE_NAMING_CAMEL_CASE:
                return $accessorPrefix . preg_replace('/_(\w)/', '\\1', $propertyName) ?: null;

            case PropertyMetadata::ACCESS_TYPE_NAMING_EXACT:
                return $accessorPrefix . $propertyName;
        }

        throw new RuntimeException("Undefined naming type '$naming'.");
    }

    protected function getSetter(PropertyMetadata $metadata, $naming)
    {
        if ($metadata->setter || $metadata->readOnly) {
            return $metadata->setter;
        }

        $class = $this->getDeclaringClass($metadata);
        $name = $metadata->name;

        if (null !== $method = $this->findAccessorName($this->setterPrefixes, $name, $class, $naming)) {
            return $method;
        }

        throw new RuntimeException("Specify public setter for {$class->getName()}::\${$name} (using $naming naming)");
    }

    protected function getGetter(PropertyMetadata $metadata, $naming)
    {
        if ($metadata->getter) {
            return $metadata->getter;
        }

        $class = $this->getDeclaringClass($metadata);
        $name = $metadata->name;


        if (null !== $method = $this->findAccessorName($this->getterPrefixes, $name, $class, $naming)) {
            return $method;
        }

        throw new RuntimeException("Specify public getter for {$class->getName()}::\${$name} (using $naming naming)");
    }

    /**
     * @param PropertyMetadata $metadata
     * @return \ReflectionClass
     */
    protected function getDeclaringClass(PropertyMetadata $metadata)
    {
        return $metadata->getReflection()->getDeclaringClass();
    }

    /**
     * @param array $prefixes
     * @param string $name
     * @param \ReflectionClass $class
     * @param string $naming
     *
     * @return null|string
     */
    protected function findAccessorName(array $prefixes, $name, \ReflectionClass $class, $naming)
    {
        foreach ($prefixes as $prefix) {
            $accessorName = $this->getAccessorName($prefix, $name, $naming);
            if ($this->hasPublicMethod($class, $accessorName)) {
                return $accessorName;
            }
        }

        return null;
    }

    /**
     * @param \ReflectionClass $class
     * @param string $method
     * @return bool
     */
    protected function hasPublicMethod(\ReflectionClass $class, $method)
    {
        return $class->hasMethod($method) && $class->getMethod($method)->isPublic();
    }

    protected function updateAccessors(PropertyMetadata $propertyMetadata, ClassMetadata $classMetadata)
    {
        $type =
            $propertyMetadata->accessType ?:
                $classMetadata->accessType ?:
                    $this->defaultType;

        if ($type !== PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD) {
            return;
        }

        $naming =
            $propertyMetadata->accessTypeNaming ?:
                $classMetadata->accessTypeNaming ?:
                    $this->defaultNaming;

        $propertyMetadata->setter = $propertyMetadata->setter ?: $this->getSetter($propertyMetadata, $naming);
        $propertyMetadata->getter = $propertyMetadata->getter ?: $this->getGetter($propertyMetadata, $naming);
    }
}
