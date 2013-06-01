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

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Metadata\MethodMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Metadata\ClassMetadata;
use Symfony\Component\Yaml\Yaml;
use Metadata\Driver\AbstractFileDriver;

class YamlDriver extends AbstractFileDriver
{
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config[$name = $class->name])) {
            throw new RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $class->name, $file));
        }

        $config = $config[$name];
        $metadata = new ClassMetadata($name);
        $metadata->fileResources[] = $file;
        $metadata->fileResources[] = $class->getFileName();
        $exclusionPolicy = isset($config['exclusion_policy']) ? strtoupper($config['exclusion_policy']) : 'NONE';
        $excludeAll = isset($config['exclude']) ? (Boolean) $config['exclude'] : false;
        $classAccessType = isset($config['access_type']) ? $config['access_type'] : PropertyMetadata::ACCESS_TYPE_PROPERTY;

        $propertiesMetadata = array();

        if (isset($config['accessor_order'])) {
            $metadata->setAccessorOrder($config['accessor_order'], isset($config['custom_accessor_order']) ? $config['custom_accessor_order'] : array());
        }

        if (isset($config['xml_root_name'])) {
            $metadata->xmlRootName = (string) $config['xml_root_name'];
        }

        if (isset($config['discriminator'])) {
            if (isset($config['discriminator']['disabled']) && true === $config['discriminator']['disabled']) {
                $metadata->discriminatorDisabled = true;
            } else {
                if ( ! isset($config['discriminator']['field_name'])) {
                    throw new RuntimeException('The "field_name" attribute must be set for discriminators.');
                }

                if ( ! isset($config['discriminator']['map']) || ! is_array($config['discriminator']['map'])) {
                    throw new RuntimeException('The "map" attribute must be set, and be an array for discriminators.');
                }

                $metadata->setDiscriminator($config['discriminator']['field_name'], $config['discriminator']['map']);
            }
        }

        if (array_key_exists('virtual_properties', $config) ) {

            foreach ( $config['virtual_properties'] as $methodName => $propertySettings ) {

                if ( !$class->hasMethod( $methodName ) ) {
                    throw new RuntimeException('The method '.$methodName.' not found in class ' . $class->name);
                }

                $virtualPropertyMetadata = new VirtualPropertyMetadata( $name, $methodName );

                $propertiesMetadata[$methodName] = $virtualPropertyMetadata;
                $config['properties'][$methodName] = $propertySettings;
            }
        }

        if (!$excludeAll) {
            foreach ($class->getProperties() as $property) {
                if ($name !== $property->class) {
                    continue;
                }
                $pName = $property->getName();
                $propertiesMetadata[$pName] = new PropertyMetadata($name, $pName);
            }

            foreach ($propertiesMetadata as $pName => $pMetadata) {

                $isExclude = false;
                $isExpose = $pMetadata instanceof VirtualPropertyMetadata;

                if (isset($config['properties'][$pName])) {
                    $pConfig = $config['properties'][$pName];

                    if (isset($pConfig['exclude'])) {
                        $isExclude = (Boolean) $pConfig['exclude'];
                    }

                    if (isset($pConfig['expose'])) {
                        $isExpose = (Boolean) $pConfig['expose'];
                    }

                    if (isset($pConfig['since_version'])) {
                        $pMetadata->sinceVersion = (string) $pConfig['since_version'];
                    }

                    if (isset($pConfig['until_version'])) {
                        $pMetadata->untilVersion = (string) $pConfig['until_version'];
                    }

                    if (isset($pConfig['serialized_name'])) {
                        $pMetadata->serializedName = (string) $pConfig['serialized_name'];
                    }

                    if (isset($pConfig['type'])) {
                        $pMetadata->setType((string) $pConfig['type']);
                    }

                    if (isset($pConfig['groups'])) {
                        $pMetadata->groups = $pConfig['groups'];
                    }

                    if (isset($pConfig['xml_list'])) {
                        $pMetadata->xmlCollection = true;

                        $colConfig = $pConfig['xml_list'];
                        if (isset($colConfig['inline'])) {
                            $pMetadata->xmlCollectionInline = (Boolean) $colConfig['inline'];
                        }

                        if (isset($colConfig['entry_name'])) {
                            $pMetadata->xmlEntryName = (string) $colConfig['entry_name'];
                        }
                    }

                    if (isset($pConfig['xml_map'])) {
                        $pMetadata->xmlCollection = true;

                        $colConfig = $pConfig['xml_map'];
                        if (isset($colConfig['inline'])) {
                            $pMetadata->xmlCollectionInline = (Boolean) $colConfig['inline'];
                        }

                        if (isset($colConfig['entry_name'])) {
                            $pMetadata->xmlEntryName = (string) $colConfig['entry_name'];
                        }

                        if (isset($colConfig['key_attribute_name'])) {
                            $pMetadata->xmlKeyAttribute = $colConfig['key_attribute_name'];
                        }
                    }

                    if (isset($pConfig['xml_attribute'])) {
                        $pMetadata->xmlAttribute = (Boolean) $pConfig['xml_attribute'];
                    }

                    if (isset($pConfig['xml_attribute_map'])) {
                        $pMetadata->xmlAttribute = (Boolean) $pConfig['xml_attribute_map'];
                    }

                    if (isset($pConfig['xml_value'])) {
                        $pMetadata->xmlValue = (Boolean) $pConfig['xml_value'];
                    }

                    if (isset($pConfig['xml_key_value_pairs'])) {
                        $pMetadata->xmlKeyValuePairs = (Boolean) $pConfig['xml_key_value_pairs'];
                    }

                    //we need read_only before setter and getter set, because that method depends on flag being set
                    if (isset($pConfig['read_only'])) {
                          $pMetadata->readOnly = (Boolean) $pConfig['read_only'];
                    }

                    $pMetadata->setAccessor(
                        isset($pConfig['access_type']) ? $pConfig['access_type'] : $classAccessType,
                        isset($pConfig['accessor']['getter']) ? $pConfig['accessor']['getter'] : null,
                        isset($pConfig['accessor']['setter']) ? $pConfig['accessor']['setter'] : null
                    );

                    if (isset($pConfig['inline'])) {
                        $pMetadata->inline = (Boolean) $pConfig['inline'];
                    }

                    if (isset($pConfig['max_depth'])) {
                        $pMetadata->maxDepth = (int) $pConfig['max_depth'];
                    }
                }
                if ((ExclusionPolicy::NONE === $exclusionPolicy && !$isExclude)
                || (ExclusionPolicy::ALL === $exclusionPolicy && $isExpose)) {
                    $metadata->addPropertyMetadata($pMetadata);
                }
            }
        }

        if (isset($config['handler_callbacks'])) {
            foreach ($config['handler_callbacks'] as $direction => $formats) {
                foreach ($formats as $format => $methodName) {
                    $direction = GraphNavigator::parseDirection($direction);
                    $metadata->addHandlerCallback($direction, $format, $methodName);
                }
            }
        }

        if (isset($config['callback_methods'])) {
            $cConfig = $config['callback_methods'];

            if (isset($cConfig['pre_serialize'])) {
                $metadata->preSerializeMethods = $this->getCallbackMetadata($class, $cConfig['pre_serialize']);
            }
            if (isset($cConfig['post_serialize'])) {
                $metadata->postSerializeMethods = $this->getCallbackMetadata($class, $cConfig['post_serialize']);
            }
            if (isset($cConfig['post_deserialize'])) {
                $metadata->postDeserializeMethods = $this->getCallbackMetadata($class, $cConfig['post_deserialize']);
            }
        }

        return $metadata;
    }

    protected function getExtension()
    {
        return 'yml';
    }

    private function getCallbackMetadata(\ReflectionClass $class, $config)
    {
        if (is_string($config)) {
            $config = array($config);
        } elseif (!is_array($config)) {
            throw new RuntimeException(sprintf('callback methods expects a string, or an array of strings that represent method names, but got %s.', json_encode($config['pre_serialize'])));
        }

        $methods = array();
        foreach ($config as $name) {
            if (!$class->hasMethod($name)) {
                throw new RuntimeException(sprintf('The method %s does not exist in class %s.', $name, $class->name));
            }

            $methods[] = new MethodMetadata($class->name, $name);
        }

        return $methods;
    }
}
