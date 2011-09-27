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

use JMS\SerializerBundle\Exception\RuntimeException;
use JMS\SerializerBundle\Exception\XmlErrorException;
use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use Metadata\MethodMetadata;
use JMS\SerializerBundle\Metadata\ClassMetadata;
use Metadata\Driver\AbstractFileDriver;

class XmlDriver extends AbstractFileDriver
{
    protected function loadMetadataFromFile(\ReflectionClass $class, $path)
    {
        $previous = libxml_use_internal_errors(true);
        $elem = simplexml_load_file($path);
        libxml_use_internal_errors($previous);

        if (false === $elem) {
            throw new XmlErrorException(libxml_get_last_error());
        }

        $metadata = new ClassMetadata($name = $class->getName());
        if (!$elems = $elem->xpath("./class[@name = '".$name."']")) {
            throw new RuntimeException(sprintf('Could not find class %s inside XML element.', $name));
        }
        $elem = reset($elems);

        $metadata->fileResources[] = $path;
        $metadata->fileResources[] = $class->getFileName();
        $exclusionPolicy = $elem->attributes()->{'exclusion-policy'} ?: 'NONE';
        $excludeAll = null !== ($exclude = $elem->attributes()->exclude) ? 'true' === (string) $exclude : false;

        if (null !== $xmlRootName = $elem->attributes()->{'xml-root-name'}) {
            $metadata->xmlRootName = (string) $xmlRootName;
        }

        if (!$excludeAll) {
            foreach ($class->getProperties() as $property) {
                if ($name !== $property->getDeclaringClass()->getName()) {
                    continue;
                }

                $pMetadata = new PropertyMetadata($name, $pName = $property->getName());
                $isExclude = $isExpose = false;

                $pElems = $elem->xpath("./property[@name = '".$pName."']");

                if ($pElems) {
                    $pElem = reset($pElems);

                    if (null !== $exclude = $pElem->attributes()->exclude) {
                        $isExclude = 'true' === (string) $exclude;
                    }

                    if (null !== $expose = $pElem->attributes()->expose) {
                        $isExpose = 'true' === (string) $expose;
                    }

                    if (null !== $version = $pElem->attributes()->{'since-version'}) {
                        $pMetadata->sinceVersion = (string) $version;
                    }

                    if (null !== $version = $pElem->attributes()->{'until-version'}) {
                        $pMetadata->untilVersion = (string) $version;
                    }

                    if (null !== $serializedName = $pElem->attributes()->{'serialized-name'}) {
                        $pMetadata->serializedName = (string) $serializedName;
                    }

                    if (null !== $type = $pElem->attributes()->type) {
                        $pMetadata->type = (string) $type;
                    } else if (isset($pElem->type)) {
                        $pMetadata->type = (string) $pElem->type;
                    }

                    if (isset($pElem->{'xml-list'})) {
                        $pMetadata->xmlCollection = true;

                        $colConfig = $pElem->{'xml-list'};
                        if (isset($colConfig->attributes()->inline)) {
                            $pMetadata->xmlCollectionInline = 'true' === (string) $colConfig->attributes()->inline;
                        }

                        if (isset($colConfig->attributes()->{'entry-name'})) {
                            $pMetadata->xmlEntryName = (string) $colConfig->attributes()->{'entry-name'};
                        }
                    }

                    if (isset($pElem->{'xml-map'})) {
                        $pMetadata->xmlCollection = true;

                        $colConfig = $pElem->{'xml-map'};
                        if (isset($colConfig->attributes()->inline)) {
                            $pMetadata->xmlCollectionInline = 'true' === $colConfig->attributes()->inline;
                        }

                        if (isset($colConfig->attributes()->{'entry-name'})) {
                            $pMetadata->xmlEntryName = (string) $colConfig->attributes()->{'entry-name'};
                        }

                        if (isset($colConfig->attributes()->{'key-attribute-name'})) {
                            $pMetadata->xmlKeyAttribute = $colConfig->attributes()->{'key-attribute-name'};
                        }
                    }

                    if (isset($pElem->attributes()->{'xml-attribute'})) {
                        $pMetadata->xmlAttribute = 'true' === (string) $pElem->attributes()->{'xml-attribute'};
                    }
                    
                    if (isset($pElem->attributes()->{'xml-value'})) {
                        $pMetadata->xmlValue = 'true' === (string) $pElem->attributes()->{'xml-value'};
                    }

                    if ((ExclusionPolicy::NONE === (string)$exclusionPolicy && !$isExclude)
                        || (ExclusionPolicy::ALL === (string)$exclusionPolicy && $isExpose)) {

                        $metadata->addPropertyMetadata($pMetadata);
                    }
                }
            }
        }

        foreach ($elem->xpath('./callback-method') as $method) {
            if (!isset($method->attributes()->type)) {
                throw new RuntimeException('The type attribute must be set for all callback-method elements.');
            }
            if (!isset($method->attributes()->name)) {
                throw new RuntimeException('The name attribute must be set for all callback-method elements.');
            }

            switch ((string) $method->attributes()->type) {
                case 'pre-serialize':
                    $metadata->addPreSerializeMethod(new MethodMetadata($name, (string) $method->attributes()->name));
                    break;

                case 'post-serialize':
                    $metadata->addPostSerializeMethod(new MethodMetadata($name, (string) $method->attributes()->name));
                    break;

                case 'post-deserialize':
                    $metadata->addPostDeserializeMethod(new MethodMetadata($name, (string) $method->attributes()->name));
                    break;

                default:
                    throw new RuntimeException(sprintf('The type "%s" is not supported.', $method->attributes()->name));
            }
        }

        return $metadata;
    }

    protected function getExtension()
    {
        return 'xml';
    }
}