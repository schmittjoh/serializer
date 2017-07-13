<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Metadata\Driver;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use Metadata\Driver\DriverInterface;

/**
 * This class decorates any other driver. If the inner driver does not provide a
 * a property type, the decorator will guess based on Doctrine 2 metadata.
 */
abstract class AbstractDoctrineTypeDriver implements DriverInterface
{
    /**
     * Map of doctrine 2 field types to JMS\Serializer types
     * @var array
     */
    protected $fieldMapping = array(
        'string' => 'string',
        'text' => 'string',
        'blob' => 'string',

        'integer' => 'integer',
        'smallint' => 'integer',
        'bigint' => 'integer',

        'datetime' => 'DateTime',
        'datetimetz' => 'DateTime',
        'time' => 'DateTime',
        'date' => 'DateTime',

        'float' => 'float',
        'decimal' => 'float',

        'boolean' => 'boolean',

        'array' => 'array',
        'json_array' => 'array',
        'simple_array' => 'array<string>',
    );

    /**
     * @var DriverInterface
     */
    protected $delegate;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    public function __construct(DriverInterface $delegate, ManagerRegistry $registry)
    {
        $this->delegate = $delegate;
        $this->registry = $registry;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        /** @var $classMetadata ClassMetadata */
        $classMetadata = $this->delegate->loadMetadataForClass($class);

        // Abort if the given class is not a mapped entity
        if (!$doctrineMetadata = $this->tryLoadingDoctrineMetadata($class->name)) {
            return $classMetadata;
        }

        $this->setDiscriminator($doctrineMetadata, $classMetadata);

        // We base our scan on the internal driver's property list so that we
        // respect any internal white/blacklisting like in the AnnotationDriver
        foreach ($classMetadata->propertyMetadata as $key => $propertyMetadata) {
            /** @var $propertyMetadata PropertyMetadata */

            // If the inner driver provides a type, don't guess anymore.
            if ($propertyMetadata->type || $this->isVirtualProperty($propertyMetadata)) {
                continue;
            }

            if ($this->hideProperty($doctrineMetadata, $propertyMetadata)) {
                unset($classMetadata->propertyMetadata[$key]);
            }

            $this->setPropertyType($doctrineMetadata, $propertyMetadata);
        }

        return $classMetadata;
    }

    private function isVirtualProperty(PropertyMetadata $propertyMetadata)
    {
        return $propertyMetadata instanceof VirtualPropertyMetadata
            || $propertyMetadata instanceof StaticPropertyMetadata
            || $propertyMetadata instanceof ExpressionPropertyMetadata;
    }

    /**
     * @param DoctrineClassMetadata $doctrineMetadata
     * @param ClassMetadata $classMetadata
     */
    protected function setDiscriminator(DoctrineClassMetadata $doctrineMetadata, ClassMetadata $classMetadata)
    {
    }

    /**
     * @param DoctrineClassMetadata $doctrineMetadata
     * @param PropertyMetadata $propertyMetadata
     */
    protected function hideProperty(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata)
    {
        return false;
    }

    /**
     * @param DoctrineClassMetadata $doctrineMetadata
     * @param PropertyMetadata $propertyMetadata
     */
    protected function setPropertyType(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata)
    {
    }

    /**
     * @param string $className
     *
     * @return null|DoctrineClassMetadata
     */
    protected function tryLoadingDoctrineMetadata($className)
    {
        if (!$manager = $this->registry->getManagerForClass($className)) {
            return null;
        }

        if ($manager->getMetadataFactory()->isTransient($className)) {
            return null;
        }

        return $manager->getClassMetadata($className);
    }

    /**
     * @param string $type
     */
    protected function normalizeFieldType($type)
    {
        if (!isset($this->fieldMapping[$type])) {
            return;
        }

        return $this->fieldMapping[$type];
    }
}
