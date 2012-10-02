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

namespace JMS\SerializerBundle\Serializer;

use JMS\SerializerBundle\Exception\XmlErrorException;

use JMS\SerializerBundle\Exception\RuntimeException;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Serializer\Construction\ObjectConstructorInterface;
use JMS\SerializerBundle\Serializer\Naming\PropertyNamingStrategyInterface;

class XmlDeserializationVisitor extends AbstractDeserializationVisitor
{
    private $objectConstructor;
    private $objectStack;
    private $metadataStack;
    private $currentObject;
    private $currentMetadata;
    private $result;
    private $navigator;
    private $disableExternalEntities;
    private $documentWhitelist = array();

    public function __construct(PropertyNamingStrategyInterface $namingStrategy, array $customHandlers, ObjectConstructorInterface $objectConstructor, $disableExternalEntities = true)
    {
        parent::__construct($namingStrategy, $customHandlers);

        $this->objectConstructor = $objectConstructor;
        $this->disableExternalEntities = $disableExternalEntities;
    }

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->objectStack = new \SplStack;
        $this->metadataStack = new \SplStack;
        $this->result = null;
    }

    public function getNavigator()
    {
        return $this->navigator;
    }

    public function prepare($data)
    {
        $previous = libxml_use_internal_errors(true);
        $previousEntityLoaderState = libxml_disable_entity_loader($this->disableExternalEntities);

        $dom = new \DOMDocument();
        $dom->loadXML($data);
        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                $internalSubset = str_replace(PHP_EOL, '', $child->internalSubset);
                if (!in_array($internalSubset, $this->documentWhitelist)) {
                    throw new \InvalidArgumentException(sprintf(
                        'The document type "%s" is not allowed. If it is safe, you may add it to the whitelist configuration.',
                        $internalSubset
                    ));
                }
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

    public function visitString($data, $type)
    {
        $data = (string) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitBoolean($data, $type)
    {
        $data = (string) $data;

        if ('true' === $data) {
            $data = true;
        } elseif ('false' === $data) {
            $data = false;
        } else {
            throw new RuntimeException(sprintf('Could not convert data to boolean. Expected "true", or "false", but got %s.', json_encode($data)));
        }

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitInteger($data, $type)
    {
        $data = (integer) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitDouble($data, $type)
    {
        $data = (double) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitArray($data, $type)
    {
        $entryName = null !== $this->currentMetadata && $this->currentMetadata->xmlEntryName ? $this->currentMetadata->xmlEntryName : 'entry';

        if (!isset($data->$entryName)) {
            if (null === $this->result) {
                return $this->result = array();
            }

            return array();
        }

        if ('array' === $type) {
            throw new RuntimeException(sprintf('You must specify either a list type, or a key and entry type for type array.'));
        }

        if (false === $pos = strpos($type, ',', 6)) {
            $listType = substr($type, 6, -1);

            $result = array();
            if (null === $this->result) {
                $this->result = &$result;
            }

            foreach ($data->$entryName as $v) {
                $result[] = $this->navigator->accept($v, $listType, $this);
            }

            return $result;
        }

        if (null === $this->currentMetadata) {
            throw new RuntimeException('Maps are not supported on top-level without metadata.');
        }

        $keyType = trim(substr($type, 6, $pos - 6));
        $entryType = trim(substr($type, $pos+1, -1));
        $result = array();
        if (null === $this->result) {
            $this->result = &$result;
        }

        foreach ($data->$entryName as $v) {
            if (!isset($v[$this->currentMetadata->xmlKeyAttribute])) {
                throw new RuntimeException(sprintf('The key attribute "%s" must be set for each entry of the map.', $this->currentMetadata->xmlKeyAttribute));
            }

            $k = $this->navigator->accept($v[$this->currentMetadata->xmlKeyAttribute], $keyType, $this);
            $result[$k] = $this->navigator->accept($v, $entryType, $this);
        }

        return $result;
    }

    public function visitTraversable($data, $type)
    {
        throw new RuntimeException('Traversable is not supported for Deserialization.');
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, $type)
    {
        $this->setCurrentObject($this->objectConstructor->construct($this, $metadata, $data, $type));

        if (null === $this->result) {
            $this->result = $this->currentObject;
        }
    }

    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $name = $this->namingStrategy->translateName($metadata);

        if (!$metadata->type) {
            throw new RuntimeException(sprintf('You must define a type for %s::$%s.', $metadata->reflection->class, $metadata->name));
        }

        if ($metadata->xmlAttribute) {
            if (isset($data[$name])) {
                $v = $this->navigator->accept($data[$name], $metadata->type, $this);
                $metadata->reflection->setValue($this->currentObject, $v);
            }

            return;
        }

        if ($metadata->xmlValue) {
            $v = $this->navigator->accept($data, $metadata->type, $this);
            $metadata->reflection->setValue($this->currentObject, $v);

            return;
        }

        if ($metadata->xmlCollection) {
            $enclosingElem = $data;
            if (!$metadata->xmlCollectionInline && isset($data->$name)) {
                $enclosingElem = $data->$name;
            }

            $this->setCurrentMetadata($metadata);
            $v = $this->navigator->accept($enclosingElem, $metadata->type, $this);
            $this->revertCurrentMetadata();
            $metadata->reflection->setValue($this->currentObject, $v);

            return;
        }

        if (!isset($data->$name)) {
            return;
        }

        $v = $this->navigator->accept($data->$name, $metadata->type, $this);

        if (null === $metadata->setter) {
            $metadata->reflection->setValue($this->currentObject, $v);

            return;
        }

        $this->currentObject->{$metadata->setter}($v);
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, $type)
    {
        $rs = $this->currentObject;
        $this->revertCurrentObject();

        return $rs;
    }

    public function visitPropertyUsingCustomHandler(PropertyMetadata $metadata, $object)
    {
        // TODO
        return false;
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

    public function setDocumentWhitelist(array $documentWhitelist)
    {
        $this->documentWhitelist = $documentWhitelist;
    }

    public function getDocumentWhitelist()
    {
        return $this->documentWhitelist;
    }
}
