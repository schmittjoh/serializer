<?php

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
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\XmlErrorException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\AdvancedNamingStrategyInterface;

class XmlDeserializationVisitor extends AbstractVisitor implements NullAwareVisitorInterface
{
    private $objectStack;
    private $metadataStack;
    private $objectMetadataStack;
    private $currentObject;
    private $currentMetadata;
    private $result;
    private $navigator;
    private $disableExternalEntities = true;
    private $doctypeWhitelist = array();

    public function enableExternalEntities()
    {
        $this->disableExternalEntities = false;
    }

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->objectStack = new \SplStack;
        $this->metadataStack = new \SplStack;
        $this->objectMetadataStack = new \SplStack;
        $this->result = null;
    }

    public function getNavigator()
    {
        return $this->navigator;
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

    public function visitNull($data, array $type, Context $context)
    {
        return null;
    }

    public function visitString($data, array $type, Context $context)
    {
        $data = (string)$data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        $data = (string)$data;

        if ('true' === $data || '1' === $data) {
            $data = true;
        } elseif ('false' === $data || '0' === $data) {
            $data = false;
        } else {
            throw new RuntimeException(sprintf('Could not convert data to boolean. Expected "true", "false", "1" or "0", but got %s.', json_encode($data)));
        }

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitInteger($data, array $type, Context $context)
    {
        $data = (integer)$data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitDouble($data, array $type, Context $context)
    {
        $data = (double)$data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitArray($data, array $type, Context $context)
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
                $k = $this->navigator->accept($key, $keyType, $context);
                $result[$k] = $this->navigator->accept($v, $entryType, $context);
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

        if (!count($nodes)) {
            if (null === $this->result) {
                return $this->result = array();
            }

            return array();
        }

        switch (count($type['params'])) {
            case 0:
                throw new RuntimeException(sprintf('The array type must be specified either as "array<T>", or "array<K,V>".'));

            case 1:
                $result = array();

                if (null === $this->result) {
                    $this->result = &$result;
                }

                foreach ($nodes as $v) {
                    $result[] = $this->navigator->accept($v, $type['params'][0], $context);
                }

                return $result;

            case 2:
                if (null === $this->currentMetadata) {
                    throw new RuntimeException('Maps are not supported on top-level without metadata.');
                }

                list($keyType, $entryType) = $type['params'];
                $result = array();
                if (null === $this->result) {
                    $this->result = &$result;
                }

                $nodes = $data->children($namespace)->$entryName;
                foreach ($nodes as $v) {
                    $attrs = $v->attributes();
                    if (!isset($attrs[$this->currentMetadata->xmlKeyAttribute])) {
                        throw new RuntimeException(sprintf('The key attribute "%s" must be set for each entry of the map.', $this->currentMetadata->xmlKeyAttribute));
                    }

                    $k = $this->navigator->accept($attrs[$this->currentMetadata->xmlKeyAttribute], $keyType, $context);
                    $result[$k] = $this->navigator->accept($v, $entryType, $context);
                }

                return $result;

            default:
                throw new LogicException(sprintf('The array type does not support more than 2 parameters, but got %s.', json_encode($type['params'])));
        }
    }

    public function startVisitingObject(ClassMetadata $metadata, $object, array $type, Context $context)
    {
        $this->setCurrentObject($object);
        $this->objectMetadataStack->push($metadata);
        if (null === $this->result) {
            $this->result = $this->currentObject;
        }
    }

    public function visitProperty(PropertyMetadata $metadata, $data, Context $context)
    {
        if ($this->namingStrategy instanceof AdvancedNamingStrategyInterface) {
            $name = $this->namingStrategy->getPropertyName($metadata, $context);
        } else {
            $name = $this->namingStrategy->translateName($metadata);
        }

        if (!$metadata->type) {
            throw new RuntimeException(sprintf('You must define a type for %s::$%s.', $metadata->reflection->class, $metadata->name));
        }

        if ($metadata->xmlAttribute) {

            $attributes = $data->attributes($metadata->xmlNamespace);
            if (isset($attributes[$name])) {
                $v = $this->navigator->accept($attributes[$name], $metadata->type, $context);
                $this->accessor->setValue($this->currentObject, $v, $metadata);
            }

            return;
        }

        if ($metadata->xmlValue) {
            $v = $this->navigator->accept($data, $metadata->type, $context);
            $this->accessor->setValue($this->currentObject, $v, $metadata);

            return;
        }

        if ($metadata->xmlCollection) {
            $enclosingElem = $data;
            if (!$metadata->xmlCollectionInline) {
                $enclosingElem = $data->children($metadata->xmlNamespace)->$name;
            }

            $this->setCurrentMetadata($metadata);
            $v = $this->navigator->accept($enclosingElem, $metadata->type, $context);
            $this->revertCurrentMetadata();
            $this->accessor->setValue($this->currentObject, $v, $metadata);

            return;
        }

        if ($metadata->xmlNamespace) {
            $node = $data->children($metadata->xmlNamespace)->$name;
            if (!$node->count()) {
                return;
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
                return;
            }
            $node = reset($nodes);
        }

        if ($metadata->xmlKeyValuePairs) {
            $this->setCurrentMetadata($metadata);
        }

        $v = $this->navigator->accept($node, $metadata->type, $context);

        $this->accessor->setValue($this->currentObject, $v, $metadata);
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
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

    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array <string> $doctypeWhitelist
     */
    public function setDoctypeWhitelist(array $doctypeWhitelist)
    {
        $this->doctypeWhitelist = $doctypeWhitelist;
    }

    /**
     * @return array<string>
     */
    public function getDoctypeWhitelist()
    {
        return $this->doctypeWhitelist;
    }

    /**
     * Retrieves internalSubset even in bugfixed php versions
     *
     * @param \DOMDocumentType $child
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
        $internalSubset = str_replace(array("\n", "\r"), '', $internalSubset);
        $internalSubset = preg_replace('/\s{2,}/', ' ', $internalSubset);
        $internalSubset = str_replace(array("[ <!", "> ]>"), array('[<!', '>]>'), $internalSubset);

        return $internalSubset;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isNull($value)
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
                && ((string) $xsiAttributes['nil'] === 'true' || (string) $xsiAttributes['nil'] === '1')
            ) {
                return true;
            }
        }

        return $value === null;
    }
}
