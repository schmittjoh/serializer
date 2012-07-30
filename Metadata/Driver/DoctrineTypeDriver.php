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

namespace JMS\SerializerBundle\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * This class decorates any other driver. If the inner driver does not provide a
 * a property type, the decorator will guess based on Doctrine 2 metadata.
 */
class DoctrineTypeDriver implements DriverInterface
{
    /**
     * Map of doctrine 2 field types to JMS\SerializerBundle types
     * @var array
     */
    protected $fieldMapping = array(
        'string'    => 'string',
        'text'      => 'string',
        'blob'      => 'string',
        
        'integer'   => 'integer',        
        'smallint'  => 'integer',
        'bigint'    => 'integer',
        
        'datetime'  => 'DateTime',
        'datetimetz'=> 'DateTime',
        'time'      => 'DateTime',

        'float'     => 'float',
        'boolean'   => 'boolean',
        'array'     => 'array<string, string>',
    );

    /**
     * @var \Metadata\Driver\DriverInterface
     */
    protected $delegate;
    
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    public function __construct(DriverInterface $delegate, $em)
    {
        $this->delegate = $delegate;

        if (!$em instanceof ObjectManager && !$em instanceof ManagerRegistry) {
            throw new \InvalidArgumentException('Must be given an instance of ObjectManager or ManagerRegistry');
        }
        $this->em = $em;
    }
    
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = $this->delegate->loadMetadataForClass($class);

        // Abort if the given class is not a mapped entity
        $dbMapping = $this->getDoctrineMetadata($class->name);
        if (!$dbMapping) {
            return $classMetadata;
        }

        // We base our scan on the internal driver's property list so that we
        // respect any internal white/blacklisting like in the AnnotationDriver
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            // If the inner driver provides a type, don't guess anymore.
            if ($propertyMetadata->type) {
                continue;
            }

            $propertyName = $propertyMetadata->name;
            if ($dbMapping->hasField($propertyName)) {
                $propertyMetadata->type = $this->normalizeFieldType( $dbMapping->getTypeOfField($propertyName) );
            } elseif($dbMapping->hasAssociation($propertyName)) {
                $targetEntity = $dbMapping->getAssociationTargetClass($propertyName);
                if ($dbMapping->isSingleValuedAssociation($propertyName)) {
                    $propertyMetadata->type = $targetEntity;
                } else {
                    $propertyMetadata->type = "ArrayCollection<{$targetEntity}>";
                }
            }
        }
            
        return $classMetadata;
    }

    protected function getDoctrineMetadata($className) {
        // Find the appropriate entity manager
        $em = null;
        if ($this->em instanceof ManagerRegistry) {
            $em = $this->em->getManagerForClass($className);
        } elseif(!$this->em->getMetadataFactory()->isTransient($className)) {
            $em = $this->em;
        }

        if ($em) {
            return $em->getClassMetadata($className);
        }
    }

    protected function normalizeFieldType($type)
    {
        if (!isset($this->fieldMapping[$type])) {
            return;
        }

        return $this->fieldMapping[$type];
    }
}
