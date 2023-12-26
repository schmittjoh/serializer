<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use JMS\Serializer\Metadata\ClassMetadata as SerializerClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class EnumPropertiesDriver implements DriverInterface
{
    /**
     * @var DriverInterface
     */
    protected $delegate;

    public function __construct(DriverInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    public function loadMetadataForClass(ReflectionClass $class): ?ClassMetadata
    {
        $classMetadata = $this->delegate->loadMetadataForClass($class);

        if (null === $classMetadata) {
            return null;
        }

        \assert($classMetadata instanceof SerializerClassMetadata);

        // We base our scan on the internal driver's property list so that we
        // respect any internal allow/blocklist like in the AnnotationDriver
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            // If the inner driver provides a type, don't guess anymore.
            if ($propertyMetadata->type || $this->isVirtualProperty($propertyMetadata)) {
                continue;
            }

            try {
                $propertyReflection = $this->getReflection($propertyMetadata);
                if ($enum = $this->getEnumReflection($propertyReflection)) {
                    $serializerType = ['name' => 'enum', 'params' => [$enum->getName(), $enum->isBacked() ? 'value' : 'name']];
                    $propertyMetadata->setType($serializerType);
                }
            } catch (ReflectionException $e) {
                continue;
            }
        }

        return $classMetadata;
    }

    private function isVirtualProperty(PropertyMetadata $propertyMetadata): bool
    {
        return $propertyMetadata instanceof VirtualPropertyMetadata
            || $propertyMetadata instanceof StaticPropertyMetadata
            || $propertyMetadata instanceof ExpressionPropertyMetadata;
    }

    private function getReflection(PropertyMetadata $propertyMetadata): ReflectionProperty
    {
        return new ReflectionProperty($propertyMetadata->class, $propertyMetadata->name);
    }

    private function getEnumReflection(ReflectionProperty $propertyReflection): ?\ReflectionEnum
    {
        $reflectionType = $propertyReflection->getType();

        if (!($reflectionType instanceof \ReflectionNamedType)) {
            return null;
        }

        return enum_exists($reflectionType->getName()) ? new \ReflectionEnum($reflectionType->getName()) : null;
    }
}
