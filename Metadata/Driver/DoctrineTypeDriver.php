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

use Metadata\Driver\DriverInterface,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping\ClassMetadataInfo;

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
     * @var Metadata\Driver\DriverInterface
     */
    protected $innerDriver;
    
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    public function __construct(DriverInterface $innerDriver, ObjectManager $em)
    {
        $this->setInnerDriver($innerDriver);
        $this->setEntityManager($em);
    }
    
    protected function setInnerDriver(DriverInterface $innerDriver)
    {
        $this->innerDriver = $innerDriver;
    }

    protected function setEntityManager($em)
    {
        if (!$em instanceof EntityManager) {
            throw new InvalidArgumentException('Only the Doctrine ORM is supported at this time');
        }
        
        // Workaround: Doctrine's hasMetadataFor() only checks loaded metadata
        // so we need to load them all, otherwise we'll raise several errors.
        $em->getMetadataFactory()->getAllMetadata();        
        
        $this->em = $em;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        // Abort if the entity manager doesn't know this object
        $classMetadata = $this->innerDriver->loadMetadataForClass($class);
        $dbMetadataFactory = $this->em->getMetadataFactory();
        if (!$dbMetadataFactory->hasMetadataFor($classMetadata->name)) {
            return $classMetadata;
        }

        // We base our scan on the internal driver's property list so that we
        // respect any internal white/blacklisting like in the AnnotationDriver
        $dbMapping = $dbMetadataFactory->getMetadataFor($classMetadata->name);
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            // If the inner driver provides a type, don't guess anymore.
            if ($propertyMetadata->type) {
                continue;
            }

            $propertyName = $propertyMetadata->name;
            if (isset($dbMapping->fieldMappings[$propertyName])) {
                $propertyMetadata->type = $this->normalizeFieldType(
                    $dbMapping->fieldMappings[$propertyName]['type']
                );
            } elseif(isset($dbMapping->associationMappings[$propertyName])) {
                $rel = $dbMapping->associationMappings[$propertyName];
                if ($rel['type'] === ClassMetadataInfo::MANY_TO_ONE || $rel['type'] === ClassMetadataInfo::ONE_TO_ONE) {
                    $propertyMetadata->type = $rel['targetEntity'];
                } else {
                    $propertyMetadata->type = 'ArrayCollection<'.$rel['targetEntity'].'>';
                }
            }
        }
            
        return $classMetadata;
    }

    protected function normalizeFieldType($type)
    {
        if (!isset($this->fieldMapping[$type])) {
            return;
        }

        return $this->fieldMapping[$type];
    }
}
