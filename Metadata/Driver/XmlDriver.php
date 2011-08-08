<?php

namespace JMS\SerializerBundle\Metadata\Driver;

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
            $error = libxml_get_last_error();
            throw new \RuntimeException(sprintf('%d: Could not parse XML: %s in %s (line: %d, column: %d)', $error->level, $error->message, $error->file, $error->line, $error->column));
        }

        $metadata = new ClassMetadata($name = $class->getName());
        if (!$elems = $elem->xpath("./class[@name = '".$name."']")) {
            throw new \RuntimeException(sprintf('Could not find class %s inside XML element.', $name));
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

                    if ((ExclusionPolicy::NONE === $exclusionPolicy && !$isExclude)
                        || (ExclusionPolicy::ALL === $exclusionPolicy && $isExpose)) {
                        $metadata->addPropertyMetadata($pMetadata);
                    }
                }
            }
        }

        foreach ($elem->xpath('./callback-method') as $method) {
            if (!isset($method->attributes()->type)) {
                throw new \RuntimeException('The type attribute must be set for all callback-method elements.');
            }
            if (!isset($method->attributes()->name)) {
                throw new \RuntimeException('The name attribute must be set for all callback-method elements.');
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
                    throw new \RuntimeException(sprintf('The type "%s" is not supported.', $method->attributes()->name));
            }
        }

        return $metadata;
    }

    protected function getExtension()
    {
        return 'xml';
    }
}