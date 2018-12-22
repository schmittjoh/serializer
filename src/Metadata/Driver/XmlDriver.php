<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Exception\XmlErrorException;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
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

class XmlDriver extends AbstractFileDriver
{
    use ExpressionMetadataTrait;

    /**
     * @var ParserInterface
     */
    private $typeParser;
    /**
     * @var PropertyNamingStrategyInterface
     */
    private $namingStrategy;

    public function __construct(FileLocatorInterface $locator, PropertyNamingStrategyInterface $namingStrategy, ?ParserInterface $typeParser = null, ?CompilableExpressionEvaluatorInterface $expressionEvaluator = null)
    {
        parent::__construct($locator);
        $this->typeParser = $typeParser ?? new Parser();
        $this->namingStrategy = $namingStrategy;
        $this->expressionEvaluator = $expressionEvaluator;
    }

    protected function loadMetadataFromFile(\ReflectionClass $class, string $path): ?BaseClassMetadata
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $elem = simplexml_load_file($path);
        libxml_use_internal_errors($previous);

        if (false === $elem) {
            throw new InvalidMetadataException('Invalid XML content for metadata', 0, new XmlErrorException(libxml_get_last_error()));
        }

        $metadata = new ClassMetadata($name = $class->name);
        if (!$elems = $elem->xpath("./class[@name = '" . $name . "']")) {
            throw new InvalidMetadataException(sprintf('Could not find class %s inside XML element.', $name));
        }
        $elem = reset($elems);

        $metadata->fileResources[] = $path;
        $fileResource =  $class->getFilename();
        if (false !== $fileResource) {
            $metadata->fileResources[] = $fileResource;
        }
        $exclusionPolicy = strtoupper((string) $elem->attributes()->{'exclusion-policy'}) ?: 'NONE';
        $exclude = $elem->attributes()->exclude;
        $excludeAll = null !== $exclude ? 'true' === strtolower((string) $exclude) : false;
        $classAccessType = (string) ($elem->attributes()->{'access-type'} ?: PropertyMetadata::ACCESS_TYPE_PROPERTY);

        $propertiesMetadata = [];
        $propertiesNodes = [];

        if (null !== $accessorOrder = $elem->attributes()->{'accessor-order'}) {
            $metadata->setAccessorOrder((string) $accessorOrder, preg_split('/\s*,\s*/', (string) $elem->attributes()->{'custom-accessor-order'}));
        }

        if (null !== $xmlRootName = $elem->attributes()->{'xml-root-name'}) {
            $metadata->xmlRootName = (string) $xmlRootName;
        }

        if (null !== $xmlRootNamespace = $elem->attributes()->{'xml-root-namespace'}) {
            $metadata->xmlRootNamespace = (string) $xmlRootNamespace;
        }
        if (null !== $xmlRootPrefix = $elem->attributes()->{'xml-root-prefix'}) {
            $metadata->xmlRootPrefix = (string) $xmlRootPrefix;
        }

        $readOnlyClass = 'true' === strtolower((string) $elem->attributes()->{'read-only'});

        $discriminatorFieldName = (string) $elem->attributes()->{'discriminator-field-name'};
        $discriminatorMap = [];
        foreach ($elem->xpath('./discriminator-class') as $entry) {
            if (!isset($entry->attributes()->value)) {
                throw new InvalidMetadataException('Each discriminator-class element must have a "value" attribute.');
            }

            $discriminatorMap[(string) $entry->attributes()->value] = (string) $entry;
        }

        if ('true' === (string) $elem->attributes()->{'discriminator-disabled'}) {
            $metadata->discriminatorDisabled = true;
        } elseif (!empty($discriminatorFieldName) || !empty($discriminatorMap)) {
            $discriminatorGroups = [];
            foreach ($elem->xpath('./discriminator-groups/group') as $entry) {
                $discriminatorGroups[] = (string) $entry;
            }
            $metadata->setDiscriminator($discriminatorFieldName, $discriminatorMap, $discriminatorGroups);
        }

        foreach ($elem->xpath('./xml-namespace') as $xmlNamespace) {
            if (!isset($xmlNamespace->attributes()->uri)) {
                throw new InvalidMetadataException('The prefix attribute must be set for all xml-namespace elements.');
            }

            if (isset($xmlNamespace->attributes()->prefix)) {
                $prefix = (string) $xmlNamespace->attributes()->prefix;
            } else {
                $prefix = null;
            }

            $metadata->registerNamespace((string) $xmlNamespace->attributes()->uri, $prefix);
        }

        foreach ($elem->xpath('./xml-discriminator') as $xmlDiscriminator) {
            if (isset($xmlDiscriminator->attributes()->attribute)) {
                $metadata->xmlDiscriminatorAttribute = 'true' === (string) $xmlDiscriminator->attributes()->attribute;
            }
            if (isset($xmlDiscriminator->attributes()->cdata)) {
                $metadata->xmlDiscriminatorCData = 'true' === (string) $xmlDiscriminator->attributes()->cdata;
            }
            if (isset($xmlDiscriminator->attributes()->namespace)) {
                $metadata->xmlDiscriminatorNamespace = (string) $xmlDiscriminator->attributes()->namespace;
            }
        }

        foreach ($elem->xpath('./virtual-property') as $method) {
            if (isset($method->attributes()->expression)) {
                $virtualPropertyMetadata = new ExpressionPropertyMetadata(
                    $name,
                    (string) $method->attributes()->name,
                    $this->parseExpression((string) $method->attributes()->expression)
                );
            } else {
                if (!isset($method->attributes()->method)) {
                    throw new InvalidMetadataException('The method attribute must be set for all virtual-property elements.');
                }
                $virtualPropertyMetadata = new VirtualPropertyMetadata($name, (string) $method->attributes()->method);
            }

            $propertiesMetadata[] = $virtualPropertyMetadata;
            $propertiesNodes[] = $method;
        }

        if (!$excludeAll) {
            foreach ($class->getProperties() as $property) {
                if ($property->class !== $name || (isset($property->info) && $property->info['class'] !== $name)) {
                    continue;
                }

                $propertiesMetadata[] = new PropertyMetadata($name, $pName = $property->getName());
                $pElems = $elem->xpath("./property[@name = '" . $pName . "']");

                $propertiesNodes[] = $pElems ? reset($pElems) : null;
            }

            foreach ($propertiesMetadata as $propertyKey => $pMetadata) {
                $isExclude = false;
                $isExpose = $pMetadata instanceof VirtualPropertyMetadata
                    || $pMetadata instanceof ExpressionPropertyMetadata;

                $pElem = $propertiesNodes[$propertyKey];
                if (!empty($pElem)) {
                    if (null !== $exclude = $pElem->attributes()->exclude) {
                        $isExclude = 'true' === strtolower((string) $exclude);
                    }

                    if ($isExclude) {
                        continue;
                    }

                    if (null !== $expose = $pElem->attributes()->expose) {
                        $isExpose = 'true' === strtolower((string) $expose);
                    }

                    if (null !== $excludeIf = $pElem->attributes()->{'exclude-if'}) {
                        $pMetadata->excludeIf = $this->parseExpression((string) $excludeIf);
                    }

                    if (null !== $skip = $pElem->attributes()->{'skip-when-empty'}) {
                        $pMetadata->skipWhenEmpty = 'true' === strtolower((string) $skip);
                    }

                    if (null !== $excludeIf = $pElem->attributes()->{'expose-if'}) {
                        $pMetadata->excludeIf = $this->parseExpression('!(' . (string) $excludeIf . ')');
                        $isExpose = true;
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
                        $pMetadata->setType($this->typeParser->parse((string) $type));
                    } elseif (isset($pElem->type)) {
                        $pMetadata->setType($this->typeParser->parse((string) $pElem->type));
                    }

                    if (null !== $groups = $pElem->attributes()->groups) {
                        $pMetadata->groups = preg_split('/\s*,\s*/', trim((string) $groups));
                    } elseif (isset($pElem->groups)) {
                        $pMetadata->groups = (array) $pElem->groups->value;
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

                        if (isset($colConfig->attributes()->{'skip-when-empty'})) {
                            $pMetadata->xmlCollectionSkipWhenEmpty = 'true' === (string) $colConfig->attributes()->{'skip-when-empty'};
                        } else {
                            $pMetadata->xmlCollectionSkipWhenEmpty = true;
                        }

                        if (isset($colConfig->attributes()->namespace)) {
                            $pMetadata->xmlEntryNamespace = (string) $colConfig->attributes()->namespace;
                        }
                    }

                    if (isset($pElem->{'xml-map'})) {
                        $pMetadata->xmlCollection = true;

                        $colConfig = $pElem->{'xml-map'};
                        if (isset($colConfig->attributes()->inline)) {
                            $pMetadata->xmlCollectionInline = 'true' === (string) $colConfig->attributes()->inline;
                        }

                        if (isset($colConfig->attributes()->{'entry-name'})) {
                            $pMetadata->xmlEntryName = (string) $colConfig->attributes()->{'entry-name'};
                        }

                        if (isset($colConfig->attributes()->namespace)) {
                            $pMetadata->xmlEntryNamespace = (string) $colConfig->attributes()->namespace;
                        }

                        if (isset($colConfig->attributes()->{'key-attribute-name'})) {
                            $pMetadata->xmlKeyAttribute = (string) $colConfig->attributes()->{'key-attribute-name'};
                        }
                    }

                    if (isset($pElem->{'xml-element'})) {
                        $colConfig = $pElem->{'xml-element'};
                        if (isset($colConfig->attributes()->cdata)) {
                            $pMetadata->xmlElementCData = 'true' === (string) $colConfig->attributes()->cdata;
                        }

                        if (isset($colConfig->attributes()->namespace)) {
                            $pMetadata->xmlNamespace = (string) $colConfig->attributes()->namespace;
                        }
                    }

                    if (isset($pElem->attributes()->{'xml-attribute'})) {
                        $pMetadata->xmlAttribute = 'true' === (string) $pElem->attributes()->{'xml-attribute'};
                    }

                    if (isset($pElem->attributes()->{'xml-attribute-map'})) {
                        $pMetadata->xmlAttributeMap = 'true' === (string) $pElem->attributes()->{'xml-attribute-map'};
                    }

                    if (isset($pElem->attributes()->{'xml-value'})) {
                        $pMetadata->xmlValue = 'true' === (string) $pElem->attributes()->{'xml-value'};
                    }

                    if (isset($pElem->attributes()->{'xml-key-value-pairs'})) {
                        $pMetadata->xmlKeyValuePairs = 'true' === (string) $pElem->attributes()->{'xml-key-value-pairs'};
                    }

                    if (isset($pElem->attributes()->{'max-depth'})) {
                        $pMetadata->maxDepth = (int) $pElem->attributes()->{'max-depth'};
                    }

                    //we need read-only before setter and getter set, because that method depends on flag being set
                    if (null !== $readOnly = $pElem->attributes()->{'read-only'}) {
                        $pMetadata->readOnly = 'true' === strtolower((string) $readOnly);
                    } else {
                        $pMetadata->readOnly = $pMetadata->readOnly || $readOnlyClass;
                    }

                    $getter = $pElem->attributes()->{'accessor-getter'};
                    $setter = $pElem->attributes()->{'accessor-setter'};
                    $pMetadata->setAccessor(
                        (string) ($pElem->attributes()->{'access-type'} ?: $classAccessType),
                        $getter ? (string) $getter : null,
                        $setter ? (string) $setter : null
                    );

                    if (null !== $inline = $pElem->attributes()->inline) {
                        $pMetadata->inline = 'true' === strtolower((string) $inline);
                    }
                }

                if ($pMetadata->inline) {
                    $metadata->isList = $metadata->isList || PropertyMetadata::isCollectionList($pMetadata->type);
                    $metadata->isMap = $metadata->isMap || PropertyMetadata::isCollectionMap($pMetadata->type);
                }

                if (!$pMetadata->serializedName) {
                    $pMetadata->serializedName = $this->namingStrategy->translateName($pMetadata);
                }

                if (!empty($pElem) && null !== $name = $pElem->attributes()->name) {
                    $pMetadata->name = (string) $name;
                }

                if ((ExclusionPolicy::NONE === (string) $exclusionPolicy && !$isExclude)
                    || (ExclusionPolicy::ALL === (string) $exclusionPolicy && $isExpose)
                ) {
                    $metadata->addPropertyMetadata($pMetadata);
                }
            }
        }

        foreach ($elem->xpath('./callback-method') as $method) {
            if (!isset($method->attributes()->type)) {
                throw new InvalidMetadataException('The type attribute must be set for all callback-method elements.');
            }
            if (!isset($method->attributes()->name)) {
                throw new InvalidMetadataException('The name attribute must be set for all callback-method elements.');
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

                case 'handler':
                    if (!isset($method->attributes()->format)) {
                        throw new InvalidMetadataException('The format attribute must be set for "handler" callback methods.');
                    }
                    if (!isset($method->attributes()->direction)) {
                        throw new InvalidMetadataException('The direction attribute must be set for "handler" callback methods.');
                    }

                    break;

                default:
                    throw new InvalidMetadataException(sprintf('The type "%s" is not supported.', $method->attributes()->name));
            }
        }

        return $metadata;
    }

    protected function getExtension(): string
    {
        return 'xml';
    }
}
