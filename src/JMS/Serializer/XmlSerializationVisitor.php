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

namespace JMS\Serializer;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * XmlSerializationVisitor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class XmlSerializationVisitor extends AbstractVisitor
{
    public $document;

    private $navigator;
    private $defaultRootName = 'result';
    private $defaultVersion = '1.0';
    private $defaultEncoding = 'UTF-8';
    private $stack;
    private $metadataStack;
    private $currentNode;
    private $currentMetadata;
    private $hasValue;

    public function setDefaultRootName($name)
    {
        $this->defaultRootName = $name;
    }

    public function setDefaultVersion($version)
    {
        $this->defaultVersion = $version;
    }

    public function setDefaultEncoding($encoding)
    {
        $this->defaultEncoding = $encoding;
    }

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->document = null;
        $this->stack = new \SplStack;
        $this->metadataStack = new \SplStack;
    }

    public function getNavigator()
    {
        return $this->navigator;
    }

    public function visitNull($data, array $type, Context $context)
    {
        if (null === $this->document) {
            $this->document = $this->createDocument(null, null, true);
            $node = $this->document->createAttribute('xsi:nil');
            $node->value = 'true';
            $this->currentNode->appendChild($node);

            return;
        }

        $node = $this->document->createAttribute('xsi:nil');
        $node->value = 'true';

        return $node;
    }

    public function visitString($data, array $type, Context $context)
    {
        if (null === $this->document) {
            $this->document = $this->createDocument(null, null, true);
            $this->currentNode->appendChild($this->document->createCDATASection($data));

            return;
        }

        return $this->document->createCDATASection($data);
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        if (null === $this->document) {
            $this->document = $this->createDocument(null, null, true);
            $this->currentNode->appendChild($this->document->createTextNode($data ? 'true' : 'false'));

            return;
        }

        return $this->document->createTextNode($data ? 'true' : 'false');
    }

    public function visitInteger($data, array $type, Context $context)
    {
        return $this->visitNumeric($data, $type);
    }

    public function visitDouble($data, array $type, Context $context)
    {
        return $this->visitNumeric($data, $type);
    }

    public function visitArray($data, array $type, Context $context)
    {
        if (null === $this->document) {
            $this->document = $this->createDocument(null, null, true);
        }

        $entryName = (null !== $this->currentMetadata && null !== $this->currentMetadata->xmlEntryName) ? $this->currentMetadata->xmlEntryName : 'entry';
        $keyAttributeName = (null !== $this->currentMetadata && null !== $this->currentMetadata->xmlKeyAttribute) ? $this->currentMetadata->xmlKeyAttribute : null;

        foreach ($data as $k => $v) {
            $tagName = (null !== $this->currentMetadata && $this->currentMetadata->xmlKeyValuePairs && $this->isElementNameValid($k)) ? $k : $entryName;

            $entryNode = $this->document->createElement($tagName);
            $this->currentNode->appendChild($entryNode);
            $this->setCurrentNode($entryNode);

            if (null !== $keyAttributeName) {
                $entryNode->setAttribute($keyAttributeName, (string) $k);
            }

            if (null !== $node = $this->navigator->accept($v, isset($type['params'][1]) ? $type['params'][1] : null, $context)) {
                $this->currentNode->appendChild($node);
            }

            $this->revertCurrentNode();
        }
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        if (null === $this->document) {
            $this->document = $this->createDocument(null, null, false);
            $this->document->appendChild($this->currentNode = $this->document->createElement($metadata->xmlRootName ?: $this->defaultRootName));
        }

        $this->hasValue = false;
    }

    public function visitProperty(PropertyMetadata $metadata, $object, Context $context)
    {
        $v = $metadata->getValue($object);

        if (null === $v && !$context->shouldSerializeNull()) {
            return;
        }

        if ($metadata->xmlAttribute) {
            $node = $this->navigator->accept($v, $metadata->type, $context);
            if (!$node instanceof \DOMCharacterData) {
                throw new RuntimeException(sprintf('Unsupported value for XML attribute. Expected character data, but got %s.', json_encode($v)));
            }

            $this->currentNode->setAttribute($this->namingStrategy->translateName($metadata), $node->nodeValue);

            return;
        }

        if (($metadata->xmlValue && $this->currentNode->childNodes->length > 0)
            || (!$metadata->xmlValue && $this->hasValue)) {
            throw new RuntimeException(sprintf('If you make use of @XmlValue, all other properties in the class must have the @XmlAttribute annotation. Invalid usage detected in class %s.', $metadata->class));
        }

        if ($metadata->xmlValue) {
            $this->hasValue = true;

            $node = $this->navigator->accept($v, $metadata->type, $context);
            if (!$node instanceof \DOMCharacterData) {
                throw new RuntimeException(sprintf('Unsupported value for property %s::$%s. Expected character data, but got %s.', $metadata->reflection->class, $metadata->reflection->name, is_object($node) ? get_class($node) : gettype($node)));
            }

            $this->currentNode->appendChild($node);

            return;
        }

        if ($metadata->xmlAttributeMap) {
            if (!is_array($v)) {
                throw new RuntimeException(sprintf('Unsupported value type for XML attribute map. Expected array but got %s.', gettype($v)));
            }

            foreach ($v as $key => $value) {
                $node = $this->navigator->accept($value, null, $context);
                if (!$node instanceof \DOMCharacterData) {
                    throw new RuntimeException(sprintf('Unsupported value for a XML attribute map value. Expected character data, but got %s.', json_encode($v)));
                }

                $this->currentNode->setAttribute($key, $node->nodeValue);
            }

            return;
        }

        if ($addEnclosingElement = (!$metadata->xmlCollection || !$metadata->xmlCollectionInline) && !$metadata->inline) {
            $element = $this->document->createElement($this->namingStrategy->translateName($metadata));
            $this->setCurrentNode($element);
        }

        $this->setCurrentMetadata($metadata);

        if (null !== $node = $this->navigator->accept($v, $metadata->type, $context)) {
            $this->currentNode->appendChild($node);
        }

        $this->revertCurrentMetadata();

        if ($addEnclosingElement) {
            $this->revertCurrentNode();

            if ($element->hasChildNodes() || $element->hasAttributes()
                || (isset($metadata->type['name']) && $metadata->type['name'] === 'array' && isset($metadata->type['params'][1]))) {
                $this->currentNode->appendChild($element);
            }
        }

        $this->hasValue = false;
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
    }

    public function getResult()
    {
        return $this->document->saveXML();
    }

    public function getCurrentNode()
    {
        return $this->currentNode;
    }

    public function getCurrentMetadata()
    {
        return $this->currentMetadata;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function setCurrentMetadata(PropertyMetadata $metadata)
    {
        $this->metadataStack->push($this->currentMetadata);
        $this->currentMetadata = $metadata;
    }

    public function setCurrentNode(\DOMNode $node)
    {
        $this->stack->push($this->currentNode);
        $this->currentNode = $node;
    }

    public function revertCurrentNode()
    {
        return $this->currentNode = $this->stack->pop();
    }

    public function revertCurrentMetadata()
    {
        return $this->currentMetadata = $this->metadataStack->pop();
    }

    public function createDocument($version = null, $encoding = null, $addRoot = true)
    {
        $doc = new \DOMDocument($version ?: $this->defaultVersion, $encoding ?: $this->defaultEncoding);
        $doc->formatOutput = true;

        if ($addRoot) {
            $this->setCurrentNode($rootNode = $doc->createElement($this->defaultRootName));
            $doc->appendChild($rootNode);
        }

        return $doc;
    }

    private function visitNumeric($data, array $type)
    {
        if (null === $this->document) {
            $this->document = $this->createDocument(null, null, true);
            $this->currentNode->appendChild($textNode = $this->document->createTextNode((string) $data));

            return $textNode;
        }

        return $this->document->createTextNode((string) $data);
    }

    /**
     * Checks that the name is a valid XML element name.
     *
     * @param string $name
     *
     * @return boolean
     */
    private function isElementNameValid($name)
    {
        return $name && false === strpos($name, ' ') && preg_match('#^[\pL_][\pL0-9._-]*$#ui', $name);
    }
}
