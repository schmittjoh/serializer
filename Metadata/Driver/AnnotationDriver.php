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

use JMS\SerializerBundle\Annotation\AccessorOrder;

use JMS\SerializerBundle\Annotation\Accessor;

use JMS\SerializerBundle\Annotation\AccessType;

use JMS\SerializerBundle\Annotation\XmlMap;
use JMS\SerializerBundle\Annotation\XmlRoot;
use JMS\SerializerBundle\Annotation\XmlAttribute;
use JMS\SerializerBundle\Annotation\XmlList;
use JMS\SerializerBundle\Annotation\XmlValue;
use JMS\SerializerBundle\Annotation\XmlKeyValuePairs;
use JMS\SerializerBundle\Annotation\PostSerialize;
use JMS\SerializerBundle\Annotation\PostDeserialize;
use JMS\SerializerBundle\Annotation\PreSerialize;
use JMS\SerializerBundle\Annotation\VirtualProperty;
use Metadata\MethodMetadata;
use Doctrine\Common\Annotations\Reader;
use JMS\SerializerBundle\Annotation\Type;
use JMS\SerializerBundle\Annotation\Exclude;
use JMS\SerializerBundle\Annotation\Groups;
use JMS\SerializerBundle\Annotation\Expose;
use JMS\SerializerBundle\Annotation\SerializedName;
use JMS\SerializerBundle\Annotation\Until;
use JMS\SerializerBundle\Annotation\Since;
use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use JMS\SerializerBundle\Annotation\Inline;
use JMS\SerializerBundle\Annotation\ReadOnly;
use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\VirtualPropertyMetadata;
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
        $classMetadata->fileResources[] = $class->getFilename();

        $propertiesMetadata = array();
        $propertiesAnnotations = array();

        $exclusionPolicy = 'NONE';
        $excludeAll = false;
        $classAccessType = PropertyMetadata::ACCESS_TYPE_PROPERTY;
        foreach ($this->reader->getClassAnnotations($class) as $annot) {
            if ($annot instanceof ExclusionPolicy) {
                $exclusionPolicy = $annot->policy;
            } else if ($annot instanceof XmlRoot) {
                $classMetadata->xmlRootName = $annot->name;
            } else if ($annot instanceof Exclude) {
                $excludeAll = true;
            } else if ($annot instanceof AccessType) {
                $classAccessType = $annot->type;
            } else if ($annot instanceof AccessorOrder) {
                $classMetadata->setAccessorOrder($annot->order, $annot->custom);
            }
        }

        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() !== $name) {
                continue;
            }

            $methodAnnotations = $this->reader->getMethodAnnotations($method);

            foreach ($methodAnnotations as $annot) {
                if ($annot instanceof PreSerialize) {
                    $classMetadata->addPreSerializeMethod(new MethodMetadata($name, $method->getName()));
                    continue 2;
                } else if ($annot instanceof PostDeserialize) {
                    $classMetadata->addPostDeserializeMethod(new MethodMetadata($name, $method->getName()));
                    continue 2;
                } else if ($annot instanceof PostSerialize) {
                    $classMetadata->addPostSerializeMethod(new MethodMetadata($name, $method->getName()));
                    continue 2;
                } else if ($annot instanceof VirtualProperty) {
                    $virtualPropertyMetadata = new VirtualPropertyMetadata($name, $method->getName());
                    $propertiesMetadata[] = $virtualPropertyMetadata;
                    $propertiesAnnotations[] = $methodAnnotations;
                    continue 2;
                }
            }
        }

        if (!$excludeAll) {
            foreach ($class->getProperties() as $property) {
                if ($property->getDeclaringClass()->getName() !== $name) {
                    continue;
                }
                $propertiesMetadata[] = new PropertyMetadata($name, $property->getName());
                $propertiesAnnotations[] = $this->reader->getPropertyAnnotations($property);
            }

            foreach ($propertiesMetadata as $propertyKey => $propertyMetadata) {

                $isExclude = $isExpose = false;
                $AccessType = $classAccessType;
                $accessor = array(null, null);

                $propertyAnnotations = $propertiesAnnotations[$propertyKey];

                foreach ($propertyAnnotations as $annot) {
                    if ($annot instanceof Since) {
                        $propertyMetadata->sinceVersion = $annot->version;
                    } else if ($annot instanceof Until) {
                        $propertyMetadata->untilVersion = $annot->version;
                    } else if ($annot instanceof SerializedName) {
                        $propertyMetadata->serializedName = $annot->name;
                    } else if ($annot instanceof Expose) {
                        $isExpose = true;
                    } else if ($annot instanceof Exclude) {
                        $isExclude = true;
                    } else if ($annot instanceof Type) {
                        $propertyMetadata->type = $annot->name;
                    } else if ($annot instanceof XmlList) {
                        $propertyMetadata->xmlCollection = true;
                        $propertyMetadata->xmlCollectionInline = $annot->inline;
                        $propertyMetadata->xmlEntryName = $annot->entry;
                    } else if ($annot instanceof XmlMap) {
                        $propertyMetadata->xmlCollection = true;
                        $propertyMetadata->xmlCollectionInline = $annot->inline;
                        $propertyMetadata->xmlEntryName = $annot->entry;
                        $propertyMetadata->xmlKeyAttribute = $annot->keyAttribute;
                    } else if ($annot instanceof XmlKeyValuePairs) {
                        $propertyMetadata->xmlKeyValuePairs = true;
                    } else if ($annot instanceof XmlAttribute) {
                        $propertyMetadata->xmlAttribute = true;
                    } else if ($annot instanceof XmlValue) {
                        $propertyMetadata->xmlValue = true;
                    } else if ($annot instanceof AccessType) {
                        $AccessType = $annot->type;
                    } else if ($annot instanceof Accessor) {
                        $accessor = array($annot->getter, $annot->setter);
                    } else if ($annot instanceof Groups) {
                        $propertyMetadata->groups = $annot->groups;
                    } else if ($annot instanceof Inline) {
                        $propertyMetadata->inline = true;
                    } else if ($annot instanceof ReadOnly) {
                        $propertyMetadata->readOnly = true;
                    }
                }

                $propertyMetadata->setAccessor($AccessType, $accessor[0], $accessor[1]);

                if ((ExclusionPolicy::NONE === $exclusionPolicy && !$isExclude)
                    || (ExclusionPolicy::ALL === $exclusionPolicy && $isExpose)) {
                    $classMetadata->addPropertyMetadata($propertyMetadata);
                }
            }
        }

        return $classMetadata;
    }
}
