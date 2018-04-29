<?php

declare(strict_types=1);

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
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

namespace JMS\Serializer;

use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\XmlErrorException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Type\TypeDefinition;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

final class XmlDeserializationVisitor extends AbstractVisitor implements NullAwareVisitorInterface, DeserializationVisitorInterface
{
    private $objectStack;
    private $metadataStack;
    private $objectMetadataStack;
    private $currentObject;
    private $currentMetadata;
    private $disableExternalEntities = true;
    private $doctypeWhitelist = [];

    public function __construct(
        bool $disableExternalEntities = true, array $doctypeWhitelist = [])
    {
        $this->objectStack = new \SplStack;
        $this->metadataStack = new \SplStack;
        $this->objectMetadataStack = new \SplStack;
        $this->disableExternalEntities = $disableExternalEntities;
        $this->doctypeWhitelist = $doctypeWhitelist;
    }

    public function prepare($data)
    {
        $data = $this->emptyStringToSpaceCharacter($data);

        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $previousEntityLoaderState = libxml_disable_entity_loader($this->disableExternalEntities);

        if (false !== stripos($data, '<!doctype')) {
            $internalSubset = $this->getDomDocumentTypeEntitySubset($data);
            if (!in_array($internalSubset, $this->doctypeWhitelist, true)) {
                throw new InvalidArgumentException(sprintf(
                    'The document type "%s" is not allowed. If it is safe, you may add it to the whitelist configuration.',
                    $internalSubset
                ));
            }
        }

        $doc = simplexml_load_string($data);

        libxml_use_internal_errors($previous);
        libxml_disable_entity_loader($previousEntityLoaderState);

        if (false === $doc) {
            throw new XmlErrorException(libxml_get_last_error());
        }

        return $doc;
    }

    private function emptyStringToSpaceCharacter($data)
    {
        return $data === '' ? ' ' : (string)$data;
    }

    public function visitNull($data, TypeDefinition $type): void
    {

    }

    public function visitString($data, TypeDefinition $type): string
    {
        return (string)$data;
    }

    public function visitBoolean($data, TypeDefinition $type): bool
    {
        $data = (string)$data;

        if ('true' === $data || '1' === $data) {
            return true;
        } elseif ('false' === $data || '0' === $data) {
            return false;
        } else {
            throw new RuntimeException(sprintf('Could not convert data to boolean. Expected "true", "false", "1" or "0", but got %s.', json_encode($data)));
        }
    }

    public function visitInteger($data, TypeDefinition $type): int
    {
        return (integer)$data;
    }

    public function visitDouble($data, TypeDefinition $type): float
    {
        return (double)$data;
    }

    public function visitArray($data, TypeDefinition $type): array
    {
        // handle key-value-pairs
        if (null !== $this->currentMetadata && $this->currentMetadata->xmlKeyValuePairs) {
            if (2 !== count($type['params'])) {
                throw new RuntimeException('The array type must be specified as "array<K,V>" for Key-Value-Pairs.');
            }
            $this->revertCurrentMetadata();

            list($keyType, $entryType) = $type['params'];

            $result = [];
            foreach ($data as $key => $v) {
                $k = $this->navigator->accept($key, $keyType);
                $result[$k] = $this->navigator->accept($v, $entryType);
            }

            return $result;
        }

        $entryName = null !== $this->currentMetadata && $this->currentMetadata->xmlEntryName ? $this->currentMetadata->xmlEntryName : 'entry';
        $namespace = null !== $this->currentMetadata && $this->currentMetadata->xmlEntryNamespace ? $this->currentMetadata->xmlEntryNamespace : null;

        if ($namespace === null && $this->objectMetadataStack->count()) {
            $classMetadata = $this->objectMetadataStack->top();
            $namespace = isset($classMetadata->xmlNamespaces['']) ? $classMetadata->xmlNamespaces[''] : $namespace;
            if ($namespace === null) {
                $namespaces = $data->getDocNamespaces();
                if (isset($namespaces[''])) {
                    $namespace = $namespaces[''];
                }
            }
        }

        if (null !== $namespace) {
            $prefix = uniqid('ns-');
            $data->registerXPathNamespace($prefix, $namespace);
            $nodes = $data->xpath("$prefix:$entryName");
        } else {
            $nodes = $data->xpath($entryName);
        }

        if (!\count($nodes)) {
            return [];
        }

        switch (\count($type['params'])) {
            case 0:
                throw new RuntimeException(sprintf('The array type must be specified either as "array<T>", or "array<K,V>".'));

            case 1:
                $result = [];

                foreach ($nodes as $v) {
                    $result[] = $this->navigator->accept($v, $type['params'][0]);
                }

                return $result;

            case 2:
                if (null === $this->currentMetadata) {
                    throw new RuntimeException('Maps are not supported on top-level without metadata.');
                }

                list($keyType, $entryType) = $type['params'];
                $result = [];

                $nodes = $data->children($namespace)->$entryName;
                foreach ($nodes as $v) {
                    $attrs = $v->attributes();
                    if (!isset($attrs[$this->currentMetadata->xmlKeyAttribute])) {
                        throw new RuntimeException(sprintf('The key attribute "%s" must be set for each entry of the map.', $this->currentMetadata->xmlKeyAttribute));
                    }

                    $k = $this->navigator->accept($attrs[$this->currentMetadata->xmlKeyAttribute], $keyType);
                    $result[$k] = $this->navigator->accept($v, $entryType);
                }

                return $result;

            default:
                throw new LogicException(sprintf('The array type does not support more than 2 parameters, but got %s.', json_encode($type['params'])));
        }
    }

    public function visitDiscriminatorMapProperty($data, ClassMetadata $metadata): string
    {
        switch (true) {
            // Check XML attribute without namespace for discriminatorFieldName
            case $metadata->xmlDiscriminatorAttribute && null === $metadata->xmlDiscriminatorNamespace && isset($data->attributes()->{$metadata->discriminatorFieldName}):
                return (string)$data->attributes()->{$metadata->discriminatorFieldName};

            // Check XML attribute with namespace for discriminatorFieldName
            case $metadata->xmlDiscriminatorAttribute && null !== $metadata->xmlDiscriminatorNamespace && isset($data->attributes($metadata->xmlDiscriminatorNamespace)->{$metadata->discriminatorFieldName}):
                return (string)$data->attributes($metadata->xmlDiscriminatorNamespace)->{$metadata->discriminatorFieldName};

            // Check XML element with namespace for discriminatorFieldName
            case !$metadata->xmlDiscriminatorAttribute && null !== $metadata->xmlDiscriminatorNamespace && isset($data->children($metadata->xmlDiscriminatorNamespace)->{$metadata->discriminatorFieldName}):
                return (string)$data->children($metadata->xmlDiscriminatorNamespace)->{$metadata->discriminatorFieldName};
            // Check XML element for discriminatorFieldName
            case isset($data->{$metadata->discriminatorFieldName}):
                return (string)$data->{$metadata->discriminatorFieldName};

            default:
                throw new LogicException(sprintf(
                    'The discriminator field name "%s" for base-class "%s" was not found in input data.',
                    $metadata->discriminatorFieldName,
                    $metadata->name
                ));
        }
    }

    public function startVisitingObject(ClassMetadata $metadata, object $object, TypeDefinition $type): void
    {
        $this->setCurrentObject($object);
        $this->objectMetadataStack->push($metadata);
    }

    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $name = $metadata->serializedName;

        if (!$metadata->type) {
            throw new RuntimeException(sprintf('You must define a type for %s::$%s.', $metadata->reflection->class, $metadata->name));
        }

        if ($metadata->xmlAttribute) {

            $attributes = $data->attributes($metadata->xmlNamespace);
            if (isset($attributes[$name])) {
                return $this->navigator->accept($attributes[$name], $metadata->type);
            }

            throw new NotAcceptableException();
        }

        if ($metadata->xmlValue) {
            return $this->navigator->accept($data, $metadata->type);
        }

        if ($metadata->xmlCollection) {
            $enclosingElem = $data;
            if (!$metadata->xmlCollectionInline) {
                $enclosingElem = $data->children($metadata->xmlNamespace)->$name;
            }

            $this->setCurrentMetadata($metadata);
            $v = $this->navigator->accept($enclosingElem, $metadata->type);
            $this->revertCurrentMetadata();
            return $v;
        }

        if ($metadata->xmlNamespace) {
            $node = $data->children($metadata->xmlNamespace)->$name;
            if (!$node->count()) {
                throw new NotAcceptableException();
            }
        } else {

            $namespaces = $data->getDocNamespaces();

            if (isset($namespaces[''])) {
                $prefix = uniqid('ns-');
                $data->registerXPathNamespace($prefix, $namespaces['']);
                $nodes = $data->xpath('./' . $prefix . ':' . $name);
            } else {
                $nodes = $data->xpath('./' . $name);
            }
            if (empty($nodes)) {
                throw new NotAcceptableException();
            }
            $node = reset($nodes);
        }

        if ($metadata->xmlKeyValuePairs) {
            $this->setCurrentMetadata($metadata);
        }

        return $this->navigator->accept($node, $metadata->type);
    }

    /**
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param TypeDefinition $type
     * @return mixed
     */
    public function endVisitingObject(ClassMetadata $metadata, $data, TypeDefinition $type): object
    {
        $rs = $this->currentObject;
        $this->objectMetadataStack->pop();
        $this->revertCurrentObject();

        return $rs;
    }

    public function setCurrentObject($object)
    {
        $this->objectStack->push($this->currentObject);
        $this->currentObject = $object;
    }

    public function getCurrentObject()
    {
        return $this->currentObject;
    }

    public function revertCurrentObject()
    {
        return $this->currentObject = $this->objectStack->pop();
    }

    public function setCurrentMetadata(PropertyMetadata $metadata)
    {
        $this->metadataStack->push($this->currentMetadata);
        $this->currentMetadata = $metadata;
    }

    public function getCurrentMetadata()
    {
        return $this->currentMetadata;
    }

    public function revertCurrentMetadata()
    {
        return $this->currentMetadata = $this->metadataStack->pop();
    }

    public function getResult($data)
    {
        return $data;
    }

    /**
     * Retrieves internalSubset even in bugfixed php versions
     *
     * @param string $data
     * @return string
     */
    private function getDomDocumentTypeEntitySubset($data)
    {
        $startPos = $endPos = stripos($data, '<!doctype');
        $braces = 0;
        do {
            $char = $data[$endPos++];
            if ($char === '<') {
                ++$braces;
            }
            if ($char === '>') {
                --$braces;
            }
        } while ($braces > 0);

        $internalSubset = substr($data, $startPos, $endPos - $startPos);
        $internalSubset = str_replace(["\n", "\r"], '', $internalSubset);
        $internalSubset = preg_replace('/\s{2,}/', ' ', $internalSubset);
        $internalSubset = str_replace(["[ <!", "> ]>"], ['[<!', '>]>'], $internalSubset);

        return $internalSubset;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isNull($value): bool
    {
        if ($value instanceof \SimpleXMLElement) {
            // Workaround for https://bugs.php.net/bug.php?id=75168 and https://github.com/schmittjoh/serializer/issues/817
            // If the "name" is empty means that we are on an not-existent node and subsequent operations on the object will trigger the warning:
            // "Node no longer exists"
            if ($value->getName() === "") {
                // @todo should be "true", but for collections needs a default collection value. maybe something for the 2.0
                return false;
            }

            $xsiAttributes = $value->attributes('http://www.w3.org/2001/XMLSchema-instance');
            if (isset($xsiAttributes['nil'])
                && ((string)$xsiAttributes['nil'] === 'true' || (string)$xsiAttributes['nil'] === '1')
            ) {
                return true;
            }
        }

        return $value === null;
    }
}
