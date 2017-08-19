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

namespace JMS\Serializer\Construction;

use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\ObjectConstructionException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use Metadata\ClassHierarchyMetadata;
use Metadata\Driver\DriverInterface;
use PhpOption\None;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\ClassMetadata as JMSClassMetadata;
use \Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;

/**
 * Doctrine object constructor for new (or existing) objects during deserialization.
 */
class DoctrineObjectConstructor implements ObjectConstructorInterface
{
    const ON_MISSING_NULL = 'null';
    const ON_MISSING_EXCEPTION = 'exception';
    const ON_MISSING_FALLBACK = 'fallback';
    /**
     * @var string
     */
    private $fallbackStrategy;

    private $managerRegistry;
    private $fallbackConstructor;

    /** @var AnnotationDriver */
    private $annotationDriver;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $managerRegistry Manager registry
     * @param ObjectConstructorInterface $fallbackConstructor Fallback object constructor
     * @param string $fallbackStrategy
     */
    public function __construct(ManagerRegistry $managerRegistry, ObjectConstructorInterface $fallbackConstructor, $fallbackStrategy = self::ON_MISSING_NULL)
    {
        $this->managerRegistry = $managerRegistry;
        $this->fallbackConstructor = $fallbackConstructor;
        $this->fallbackStrategy = $fallbackStrategy;
    }

    protected function getPropertyGroups(Context $context, $className, $property)
    {
        $classMetadataHierarchy = $context->getMetadataFactory()->getMetadataForClass($className);

        if (true === $classMetadataHierarchy instanceof ClassHierarchyMetadata) {
            $isClassMetadataHierarchy = true;
            $classMetadata = current($classMetadataHierarchy->classMetadata);
        } else {
            $classMetadata = $classMetadataHierarchy;
            $isClassMetadataHierarchy = false;
        }

        // up to the hierarchy
        while (null !== $classMetadata && false !== $classMetadata) {
            // limit case
            if (array_key_exists($property, $classMetadata->propertyMetadata)) {
                $propertyGroups = $classMetadata->propertyMetadata[$property]->groups;

                if (null === $propertyGroups) {
                    $propertyGroups = array();
                }

                return $propertyGroups;
            }

            if (true === $isClassMetadataHierarchy) {
                $classMetadata = next($classMetadataHierarchy->classMetadata);
            } else {
                $parent = $classMetadata->reflection->getParentClass();
                $classMetadata = ($parent)? $context->getMetadataFactory()->getMetadataForClass($parent->getName()) : null;
            }
        }

        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        // Locate possible ObjectManager
        $objectManager = $this->managerRegistry->getManagerForClass($metadata->name);

        if (!$objectManager) {
            // No ObjectManager found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Locate possible ClassMetadata
        $classMetadataFactory = $objectManager->getMetadataFactory();

        if ($classMetadataFactory->isTransient($metadata->name)) {
            // No ClassMetadata found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Managed entity, check for proxy load
        if (!is_array($data)) {
            // Single identifier, load proxy
            return $objectManager->getReference($metadata->name, $data);
        }

        // Fallback to default constructor if missing identifier(s)
        /** @var DoctrineClassMetadata $classMetadata*/
        $classMetadata  = $objectManager->getClassMetadata($metadata->name);
        $identifierList = array();
        /** @var array $deserializingGroups */
        $deserializingGroups = $context->getGroups()->getOrElse(array());

        // Avoid calling objectManager->find if the deserialization context groups exclude identification properties
        foreach ($classMetadata->getIdentifierFieldNames() as $name) {
            $missingIdentifier = !array_key_exists($name, $data);

            if (0 < count($deserializingGroups) && false === $missingIdentifier) {
                $propertyGroups = $this->getPropertyGroups($context, $metadata->name, $name);
                $groupForIdentFound = false;

                // group list match on at least one group?
                foreach ($deserializingGroups as $deserializingGroup) {
                    if (in_array($deserializingGroup, $propertyGroups)) {
                        $groupForIdentFound = true;
                        break;
                    }
                }

                $missingIdentifier = $missingIdentifier || (!$groupForIdentFound);
            }

            if (true === $missingIdentifier) {
                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }

            $identifierList[$name] = $data[$name];
        }

        // Entity update, load it from database
        $object = $objectManager->find($metadata->name, $identifierList);

        if (null === $object) {
            switch ($this->fallbackStrategy) {
                case self::ON_MISSING_NULL:
                    return null;
                case self::ON_MISSING_EXCEPTION:
                    throw new ObjectConstructionException(sprintf("Entity %s can not be found", $metadata->name));
                case self::ON_MISSING_FALLBACK:
                    return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
                default:
                    throw new InvalidArgumentException("The provided fallback strategy for the object constructor is not valid");
            }
        }

        $objectManager->initializeObject($object);

        return $object;
    }
}
