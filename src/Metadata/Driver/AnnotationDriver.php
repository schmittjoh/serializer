<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

use JMS\Serializer\Annotation\Discriminator;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Annotation\HandlerCallback;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\XmlMap;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlValue;
use JMS\Serializer\Annotation\XmlKeyValuePairs;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\PostSerialize;
use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\PreSerialize;
use JMS\Serializer\Annotation\VirtualProperty;
use Metadata\MethodMetadata;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Inline;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation\XmlAttributeMap;
use Metadata\Driver\DriverInterface;
use JMS\Serializer\Annotation\MaxDepth;

class AnnotationDriver implements DriverInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($name = $class->name);
        $classMetadata->fileResources[] = $class->getFilename();

        $propertiesMetadata = array();
        $propertiesAnnotations = array();

        $exclusionPolicy = 'NONE';
        $excludeAll = false;
        $classAccessType = PropertyMetadata::ACCESS_TYPE_PROPERTY;
        $readOnlyClass = false;
        foreach ($this->reader->getClassAnnotations($class) as $annot) {
            if ($annot instanceof ExclusionPolicy) {
                $exclusionPolicy = $annot->policy;
            } elseif ($annot instanceof XmlRoot) {
                $classMetadata->xmlRootName = $annot->name;
                $classMetadata->xmlRootNamespace = $annot->namespace;
            } elseif ($annot instanceof XmlNamespace) {
                $classMetadata->registerNamespace($annot->uri, $annot->prefix);
            } elseif ($annot instanceof Exclude) {
                $excludeAll = true;
            } elseif ($annot instanceof AccessType) {
                $classAccessType = $annot->type;
            } elseif ($annot instanceof ReadOnly) {
                $readOnlyClass = true;
            } elseif ($annot instanceof AccessorOrder) {
                $classMetadata->setAccessorOrder($annot->order, $annot->custom);
            } elseif ($annot instanceof Discriminator) {
                if ($annot->disabled) {
                    $classMetadata->discriminatorDisabled = true;
                } else {
                    $classMetadata->setDiscriminator($annot->field, $annot->map);
                }
            }
        }

        foreach ($class->getMethods() as $method) {
            if ($method->class !== $name) {
                continue;
            }

            $methodAnnotations = $this->reader->getMethodAnnotations($method);

            foreach ($methodAnnotations as $annot) {
                if ($annot instanceof PreSerialize) {
                    $classMetadata->addPreSerializeMethod(new MethodMetadata($name, $method->name));
                    continue 2;
                } elseif ($annot instanceof PostDeserialize) {
                    $classMetadata->addPostDeserializeMethod(new MethodMetadata($name, $method->name));
                    continue 2;
                } elseif ($annot instanceof PostSerialize) {
                    $classMetadata->addPostSerializeMethod(new MethodMetadata($name, $method->name));
                    continue 2;
                } elseif ($annot instanceof VirtualProperty) {
                    $virtualPropertyMetadata = new VirtualPropertyMetadata($name, $method->name);
                    $propertiesMetadata[] = $virtualPropertyMetadata;
                    $propertiesAnnotations[] = $methodAnnotations;
                    continue 2;
                } elseif ($annot instanceof HandlerCallback) {
                    $classMetadata->addHandlerCallback(GraphNavigator::parseDirection($annot->direction), $annot->format, $method->name);
                    continue 2;
                }
            }
        }

        if ( ! $excludeAll) {
            foreach ($class->getProperties() as $property) {
                if ($property->class !== $name) {
                    continue;
                }
                $propertiesMetadata[] = new PropertyMetadata($name, $property->getName());
                $propertiesAnnotations[] = $this->reader->getPropertyAnnotations($property);
            }

            foreach ($propertiesMetadata as $propertyKey => $propertyMetadata) {
                $isExclude = false;
                $isExpose = $propertyMetadata instanceof VirtualPropertyMetadata;
                $propertyMetadata->readOnly = $propertyMetadata->readOnly || $readOnlyClass;
                $accessType = $classAccessType;
                $accessor = array(null, null);

                $propertyAnnotations = $propertiesAnnotations[$propertyKey];

                foreach ($propertyAnnotations as $annot) {
                    if ($annot instanceof Since) {
                        $propertyMetadata->sinceVersion = $annot->version;
                    } elseif ($annot instanceof Until) {
                        $propertyMetadata->untilVersion = $annot->version;
                    } elseif ($annot instanceof SerializedName) {
                        $propertyMetadata->serializedName = $annot->name;
                    } elseif ($annot instanceof Expose) {
                        $isExpose = true;
                    } elseif ($annot instanceof Exclude) {
                        $isExclude = true;
                    } elseif ($annot instanceof Type) {
                        $propertyMetadata->setType($annot->name);
                    } elseif ($annot instanceof XmlElement) {
                        $propertyMetadata->xmlAttribute = false;
                        $propertyMetadata->xmlElementCData = $annot->cdata;
                        $propertyMetadata->xmlNamespace = $annot->namespace;
                    } elseif ($annot instanceof XmlList) {
                        $propertyMetadata->xmlCollection = true;
                        $propertyMetadata->xmlCollectionInline = $annot->inline;
                        $propertyMetadata->xmlEntryName = $annot->entry;
                    } elseif ($annot instanceof XmlMap) {
                        $propertyMetadata->xmlCollection = true;
                        $propertyMetadata->xmlCollectionInline = $annot->inline;
                        $propertyMetadata->xmlEntryName = $annot->entry;
                        $propertyMetadata->xmlKeyAttribute = $annot->keyAttribute;
                    } elseif ($annot instanceof XmlKeyValuePairs) {
                        $propertyMetadata->xmlKeyValuePairs = true;
                    } elseif ($annot instanceof XmlAttribute) {
                        $propertyMetadata->xmlAttribute = true;
                        $propertyMetadata->xmlNamespace = $annot->namespace;
                    } elseif ($annot instanceof XmlValue) {
                        $propertyMetadata->xmlValue = true;
                        $propertyMetadata->xmlElementCData = $annot->cdata;
                    } elseif ($annot instanceof XmlElement) {
                        $propertyMetadata->xmlElementCData = $annot->cdata;
                    } elseif ($annot instanceof AccessType) {
                        $accessType = $annot->type;
                    } elseif ($annot instanceof ReadOnly) {
                        $propertyMetadata->readOnly = $annot->readOnly;
                    } elseif ($annot instanceof Accessor) {
                        $accessor = array($annot->getter, $annot->setter);
                    } elseif ($annot instanceof Groups) {
                        $propertyMetadata->groups = $annot->groups;
                        foreach ((array) $propertyMetadata->groups as $groupName) {
                            if (false !== strpos($groupName, ',')) {
                                throw new InvalidArgumentException(sprintf(
                                    'Invalid group name "%s" on "%s", did you mean to create multiple groups?',
                                    implode(', ', $propertyMetadata->groups),
                                    $propertyMetadata->class.'->'.$propertyMetadata->name
                                ));
                            }
                        }
                    } elseif ($annot instanceof Inline) {
                        $propertyMetadata->inline = true;
                    } elseif ($annot instanceof XmlAttributeMap) {
                        $propertyMetadata->xmlAttributeMap = true;
                    } elseif ($annot instanceof MaxDepth) {
                        $propertyMetadata->maxDepth = $annot->depth;
                    }
                }

                $propertyMetadata->setAccessor($accessType, $accessor[0], $accessor[1]);

                if ((ExclusionPolicy::NONE === $exclusionPolicy && ! $isExclude)
                    || (ExclusionPolicy::ALL === $exclusionPolicy && $isExpose)) {
                    $classMetadata->addPropertyMetadata($propertyMetadata);
                }
            }
        }

        return $classMetadata;
    }
}
