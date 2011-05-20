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

use Doctrine\Common\Annotations\Reader;
use JMS\SerializerBundle\Annotation\Type;
use JMS\SerializerBundle\Annotation\Exclude;
use JMS\SerializerBundle\Annotation\Expose;
use JMS\SerializerBundle\Annotation\SerializedName;
use JMS\SerializerBundle\Annotation\Until;
use JMS\SerializerBundle\Annotation\Since;
use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use Metadata\Driver\DriverInterface;

class AnnotationDriver implements DriverInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($name = $class->getName());
        foreach ($this->reader->getClassAnnotations($class) as $annot) {
            if ($annot instanceof ExclusionPolicy) {
                $classMetadata->exclusionPolicy = $annot->policy;
            }
        }

        foreach ($class->getProperties() as $property) {
            if ($property->getDeclaringClass()->getName() !== $name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($name, $property->getName());
            foreach ($this->reader->getPropertyAnnotations($property) as $annot) {
                if ($annot instanceof Since) {
                    $propertyMetadata->sinceVersion = $annot->version;
                } else if ($annot instanceof Until) {
                    $propertyMetadata->untilVersion = $annot->version;
                } else if ($annot instanceof SerializedName) {
                    $propertyMetadata->serializedName = $annot->name;
                } else if ($annot instanceof Expose) {
                    $propertyMetadata->exposed = true;
                } else if ($annot instanceof Exclude) {
                    $propertyMetadata->excluded = true;
                } else if ($annot instanceof Type) {
                    $propertyMetadata->type = $annot->name;
                }
            }
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
}