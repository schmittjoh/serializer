<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use Metadata\ClassMetadata as BaseClassMetadata;
use Metadata\Driver\AbstractFileDriver;
use Metadata\Driver\FileLocatorInterface;
use Metadata\MethodMetadata;
use Symfony\Component\Yaml\Yaml;

class YamlDriver extends AbstractFileDriver
{
    /**
     * @var ParserInterface
     */
    private $typeParser;
    /**
     * @var PropertyNamingStrategyInterface
     */
    private $namingStrategy;

    public function __construct(FileLocatorInterface $locator, PropertyNamingStrategyInterface $namingStrategy, ?ParserInterface $typeParser = null)
    {
        parent::__construct($locator);
        $this->typeParser = $typeParser ?? new Parser();
        $this->namingStrategy = $namingStrategy;
    }

    protected function loadMetadataFromFile(\ReflectionClass $class, string $file): ?BaseClassMetadata
    {
        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config[$name = $class->name])) {
            throw new InvalidMetadataException(sprintf('Expected metadata for class %s to be defined in %s.', $class->name, $file));
        }

        $config = $config[$name];
        $metadata = new ClassMetadata($name);
        $metadata->fileResources[] = $file;
        $metadata->fileResources[] = $class->getFileName();
        $exclusionPolicy = isset($config['exclusion_policy']) ? strtoupper($config['exclusion_policy']) : 'NONE';
        $excludeAll = isset($config['exclude']) ? (bool) $config['exclude'] : false;
        $classAccessType = $config['access_type'] ?? PropertyMetadata::ACCESS_TYPE_PROPERTY;
        $readOnlyClass = isset($config['read_only']) ? (bool) $config['read_only'] : false;
        $this->addClassProperties($metadata, $config);

        $propertiesMetadata = [];
        if (array_key_exists('virtual_properties', $config)) {
            foreach ($config['virtual_properties'] as $methodName => $propertySettings) {
                if (isset($propertySettings['exp'])) {
                    $virtualPropertyMetadata = new ExpressionPropertyMetadata($name, $methodName, $propertySettings['exp']);
                    unset($propertySettings['exp']);
                } else {
                    if (!$class->hasMethod($methodName)) {
                        throw new InvalidMetadataException('The method ' . $methodName . ' not found in class ' . $class->name);
                    }
                    $virtualPropertyMetadata = new VirtualPropertyMetadata($name, $methodName);
                }

                $pName = !empty($propertySettings['name']) ? $propertySettings['name'] : $virtualPropertyMetadata->name;

                $propertiesMetadata[$pName] = $virtualPropertyMetadata;
                $config['properties'][$pName] = $propertySettings;
            }
        }

        if (!$excludeAll) {
            foreach ($class->getProperties() as $property) {
                if ($property->class !== $name || (isset($property->info) && $property->info['class'] !== $name)) {
                    continue;
                }

                $pName = $property->getName();
                $propertiesMetadata[$pName] = new PropertyMetadata($name, $pName);
            }

            foreach ($propertiesMetadata as $pName => $pMetadata) {
                $isExclude = false;
                $isExpose = $pMetadata instanceof VirtualPropertyMetadata
                    || $pMetadata instanceof ExpressionPropertyMetadata
                    || (isset($config['properties']) && array_key_exists($pName, $config['properties']));

                if (isset($config['properties'][$pName])) {
                    $pConfig = $config['properties'][$pName];

                    if (isset($pConfig['exclude'])) {
                        $isExclude = (bool) $pConfig['exclude'];
                    }

                    if ($isExclude) {
                        continue;
                    }

                    if (isset($pConfig['expose'])) {
                        $isExpose = (bool) $pConfig['expose'];
                    }

                    if (isset($pConfig['skip_when_empty'])) {
                        $pMetadata->skipWhenEmpty = (bool) $pConfig['skip_when_empty'];
                    }

                    if (isset($pConfig['since_version'])) {
                        $pMetadata->sinceVersion = (string) $pConfig['since_version'];
                    }

                    if (isset($pConfig['until_version'])) {
                        $pMetadata->untilVersion = (string) $pConfig['until_version'];
                    }

                    if (isset($pConfig['exclude_if'])) {
                        $pMetadata->excludeIf = (string) $pConfig['exclude_if'];
                    }

                    if (isset($pConfig['expose_if'])) {
                        $pMetadata->excludeIf = '!(' . $pConfig['expose_if'] . ')';
                    }

                    if (isset($pConfig['serialized_name'])) {
                        $pMetadata->serializedName = (string) $pConfig['serialized_name'];
                    }

                    if (isset($pConfig['type'])) {
                        $pMetadata->setType($this->typeParser->parse((string) $pConfig['type']));
                    }

                    if (isset($pConfig['groups'])) {
                        $pMetadata->groups = $pConfig['groups'];
                    }

                    if (isset($pConfig['xml_list'])) {
                        $pMetadata->xmlCollection = true;

                        $colConfig = $pConfig['xml_list'];
                        if (isset($colConfig['inline'])) {
                            $pMetadata->xmlCollectionInline = (bool) $colConfig['inline'];
                        }

                        if (isset($colConfig['entry_name'])) {
                            $pMetadata->xmlEntryName = (string) $colConfig['entry_name'];
                        }

                        if (isset($colConfig['skip_when_empty'])) {
                            $pMetadata->xmlCollectionSkipWhenEmpty = (bool) $colConfig['skip_when_empty'];
                        } else {
                            $pMetadata->xmlCollectionSkipWhenEmpty = true;
                        }

                        if (isset($colConfig['namespace'])) {
                            $pMetadata->xmlEntryNamespace = (string) $colConfig['namespace'];
                        }
                    }

                    if (isset($pConfig['xml_map'])) {
                        $pMetadata->xmlCollection = true;

                        $colConfig = $pConfig['xml_map'];
                        if (isset($colConfig['inline'])) {
                            $pMetadata->xmlCollectionInline = (bool) $colConfig['inline'];
                        }

                        if (isset($colConfig['entry_name'])) {
                            $pMetadata->xmlEntryName = (string) $colConfig['entry_name'];
                        }

                        if (isset($colConfig['namespace'])) {
                            $pMetadata->xmlEntryNamespace = (string) $colConfig['namespace'];
                        }

                        if (isset($colConfig['key_attribute_name'])) {
                            $pMetadata->xmlKeyAttribute = $colConfig['key_attribute_name'];
                        }
                    }

                    if (isset($pConfig['xml_element'])) {
                        $colConfig = $pConfig['xml_element'];
                        if (isset($colConfig['cdata'])) {
                            $pMetadata->xmlElementCData = (bool) $colConfig['cdata'];
                        }

                        if (isset($colConfig['namespace'])) {
                            $pMetadata->xmlNamespace = (string) $colConfig['namespace'];
                        }
                    }

                    if (isset($pConfig['xml_attribute'])) {
                        $pMetadata->xmlAttribute = (bool) $pConfig['xml_attribute'];
                    }

                    if (isset($pConfig['xml_attribute_map'])) {
                        $pMetadata->xmlAttributeMap = (bool) $pConfig['xml_attribute_map'];
                    }

                    if (isset($pConfig['xml_value'])) {
                        $pMetadata->xmlValue = (bool) $pConfig['xml_value'];
                    }

                    if (isset($pConfig['xml_key_value_pairs'])) {
                        $pMetadata->xmlKeyValuePairs = (bool) $pConfig['xml_key_value_pairs'];
                    }

                    //we need read_only before setter and getter set, because that method depends on flag being set
                    if (isset($pConfig['read_only'])) {
                        $pMetadata->readOnly = (bool) $pConfig['read_only'];
                    } else {
                        $pMetadata->readOnly = $pMetadata->readOnly || $readOnlyClass;
                    }

                    $pMetadata->setAccessor(
                        $pConfig['access_type'] ?? $classAccessType,
                        $pConfig['accessor']['getter'] ?? null,
                        $pConfig['accessor']['setter'] ?? null
                    );

                    if (isset($pConfig['inline'])) {
                        $pMetadata->inline = (bool) $pConfig['inline'];
                    }

                    if (isset($pConfig['max_depth'])) {
                        $pMetadata->maxDepth = (int) $pConfig['max_depth'];
                    }
                }

                if (!$pMetadata->serializedName) {
                    $pMetadata->serializedName = $this->namingStrategy->translateName($pMetadata);
                }

                if ($pMetadata->inline) {
                    $metadata->isList = $metadata->isList || PropertyMetadata::isCollectionList($pMetadata->type);
                    $metadata->isMap = $metadata->isMap || PropertyMetadata::isCollectionMap($pMetadata->type);
                }

                if (isset($config['properties'][$pName])) {
                    $pConfig = $config['properties'][$pName];

                    if (isset($pConfig['name'])) {
                        $pMetadata->name = (string) $pConfig['name'];
                    }
                }

                if ((ExclusionPolicy::NONE === $exclusionPolicy && !$isExclude)
                    || (ExclusionPolicy::ALL === $exclusionPolicy && $isExpose)
                ) {
                    $metadata->addPropertyMetadata($pMetadata);
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

    protected function getExtension(): string
    {
        return 'yml';
    }

    private function addClassProperties(ClassMetadata $metadata, array $config): void
    {
        if (isset($config['custom_accessor_order']) && !isset($config['accessor_order'])) {
            $config['accessor_order'] = 'custom';
        }

        if (isset($config['accessor_order'])) {
            $metadata->setAccessorOrder($config['accessor_order'], $config['custom_accessor_order'] ?? []);
        }

        if (isset($config['xml_root_name'])) {
            $metadata->xmlRootName = (string) $config['xml_root_name'];
        }

        if (isset($config['xml_root_prefix'])) {
            $metadata->xmlRootPrefix = (string) $config['xml_root_prefix'];
        }

        if (isset($config['xml_root_namespace'])) {
            $metadata->xmlRootNamespace = (string) $config['xml_root_namespace'];
        }

        if (array_key_exists('xml_namespaces', $config)) {
            foreach ($config['xml_namespaces'] as $prefix => $uri) {
                $metadata->registerNamespace($uri, $prefix);
            }
        }

        if (isset($config['discriminator'])) {
            if (isset($config['discriminator']['disabled']) && true === $config['discriminator']['disabled']) {
                $metadata->discriminatorDisabled = true;
            } else {
                if (!isset($config['discriminator']['field_name'])) {
                    throw new InvalidMetadataException('The "field_name" attribute must be set for discriminators.');
                }

                if (!isset($config['discriminator']['map']) || !\is_array($config['discriminator']['map'])) {
                    throw new InvalidMetadataException('The "map" attribute must be set, and be an array for discriminators.');
                }
                $groups = $config['discriminator']['groups'] ?? [];
                $metadata->setDiscriminator($config['discriminator']['field_name'], $config['discriminator']['map'], $groups);

                if (isset($config['discriminator']['xml_attribute'])) {
                    $metadata->xmlDiscriminatorAttribute = (bool) $config['discriminator']['xml_attribute'];
                }
                if (isset($config['discriminator']['xml_element'])) {
                    if (isset($config['discriminator']['xml_element']['cdata'])) {
                        $metadata->xmlDiscriminatorCData = (bool) $config['discriminator']['xml_element']['cdata'];
                    }
                    if (isset($config['discriminator']['xml_element']['namespace'])) {
                        $metadata->xmlDiscriminatorNamespace = (string) $config['discriminator']['xml_element']['namespace'];
                    }
                }
            }
        }
    }

    /**
     * @param string|string[] $config
     */
    private function getCallbackMetadata(\ReflectionClass $class, $config): array
    {
        if (\is_string($config)) {
            $config = [$config];
        } elseif (!\is_array($config)) {
            throw new InvalidMetadataException(sprintf('callback methods expects a string, or an array of strings that represent method names, but got %s.', json_encode($config['pre_serialize'])));
        }

        $methods = [];
        foreach ($config as $name) {
            if (!$class->hasMethod($name)) {
                throw new InvalidMetadataException(sprintf('The method %s does not exist in class %s.', $name, $class->name));
            }

            $methods[] = new MethodMetadata($class->name, $name);
        }

        return $methods;
    }
}
