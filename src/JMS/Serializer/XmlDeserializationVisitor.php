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

use JMS\Serializer\Exception\XmlErrorException;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * this visitor will parse the xml string by the DOM classes only
 * all reading/writing stuff depends on the DOM
 *
 * Class XmlDomDesirializerVisitor
 * @package JMS\Serializer
 */
class XmlDeserializationVisitor extends AbstractVisitor
{

    /**
     * @var \SplStack
     */
    private $objectStack;

    /**
     * @var \SplStack
     */
    private $metadataStack;

    private $currentObject;

    /**
     * @var PropertyMetadata
     */
    private $currentMetadata;


    private $result;

    /**
     * @var GraphNavigator $navigator
     */
    private $navigator;

    /**
     * @var bool
     */
    private $disableExternalEntities = true;

    /**
     * @var array
     */
    private $doctypeWhitelist = array();

    /**
     * @var \DOMElement $domElement
     */
    private $domElement;


    public function enableExternalEntities()
    {
        $this->disableExternalEntities = false;
    }

    /**
     * {@inheritdoc}
     */
    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->objectStack = new \SplStack;
        $this->metadataStack = new \SplStack;
        $this->result = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getNavigator()
    {
        return $this->navigator;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($data)
    {
        $previous = libxml_use_internal_errors(true);
        $previousEntityLoaderState = libxml_disable_entity_loader($this->disableExternalEntities);

        $dom = new \DOMDocument();
        $dom->loadXML($data);
        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                $internalSubset = str_replace(array("\n", "\r"), '', $child->internalSubset);
                if (!in_array($internalSubset, $this->doctypeWhitelist, true)) {
                    throw new InvalidArgumentException(sprintf(
                        'The document type "%s" is not allowed. If it is safe, you may add it to the whitelist configuration.',
                        $internalSubset
                    ));
                }
            }
        }

        libxml_use_internal_errors($previous);
        libxml_disable_entity_loader($previousEntityLoaderState);

        if (false === $dom) {
            throw new XmlErrorException(libxml_get_last_error());
        }
        return $dom;
    }

    /**
     * sets the node this visitor is visiting to an DomElement class -> handle all the same way
     *
     * @param $data
     * @throws Exception\InvalidArgumentException
     */
    private function setDomElement($data)
    {
        if ($data instanceof \DOMElement) {
            $this->domElement  = $data;
            return;
        }

        if ($data instanceof \DOMDocument) {
            $this->domElement = $data->documentElement;
            return;
        }

        if ($data instanceof \DOMNode) {
            $this->domElement = $data;
            return;
        }

        throw new InvalidArgumentException('Wrong data as DOM Element given');
    }

    /**
     * helper to get the text that is inside of a node
     *
     * @return string
     */
    private function getCurrentText()
    {
        $children = $this->domElement->childNodes;
        $text = '';
        for ($i = 0; $i < $children->length; $i++) {
            $child = $children->item($i);
            if ($child instanceof \DOMText) {
                $text .= $child->textContent;
            }
        }
        return $text;
    }

    /**
     * this method will create a list of child nodes. Instead of the normal DOMNode::childNodes property
     * i will return an array of nodes, which could be parsed like an array
     *
     */
    private function getCurrentChildNodes()
    {
        if (!$this->domElement->hasChildNodes()) {
            throw new RuntimeException('There are no child nodes');
        }
        $childNodes = $this->domElement->childNodes;
        $result = array();
        for ($i = 0; $i < $childNodes->length; $i++) {
            $result[] = $childNodes->item($i);
        }
        return $result;
    }

    /**
     * instead of the nodeList this method will return an array of nodes which should be all DOMAttr
     */
    private function getCurrentAttributes()
    {
        /** @var \DOMNodeList $nodeAttributes */
        $nodeAttributes = $this->domElement->attributes;
        $result = array();
        if (!($nodeAttributes instanceof \DOMNodeList || $nodeAttributes instanceof \DOMNamedNodeMap)) {
            return $result;
        }
        if ($nodeAttributes->length > 0) {
            for ($i = 0; $i < $nodeAttributes->length; $i++) {
                $node = $nodeAttributes->item($i);
                if ($node instanceof \DOMAttr) {
                    $result[] = $node;
                }
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function visitNull($data, array $type, Context $context)
    {
        if ($this->checkNullNode($data) || $data == null) {
            if (null === $this->result) {
                $this->result = null;
            }
            return null;
        }
        throw new RuntimeException('No null data given to visit');
    }

    /**
     * {@inheritdoc}
     */
    public function visitString($data, array $type, Context $context)
    {
        $this->setDomElement($data);
        $data = $this->domElement->textContent;

        if (null === $this->result) {
            $this->result = $data;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitBoolean($data, array $type, Context $context)
    {
        $this->setDomElement($data);
        /** put it into that bad looking stuff to see the difference */
        if ($this->domElement->textContent == 'true' || $this->domElement->textContent == 1) {
            $data = true;
        } elseif ($this->domElement->textContent == 'false' || $this->domElement->textContent == 0) {
            $data = false;
        } else {
            throw new RuntimeException(
                sprintf(
                    'Could not convert data to boolean. Expected "true", or "false",but got %s.',
                    json_encode($data)
                )
            );
        }

        if (null === $this->result) {
            $this->result = $data;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitInteger($data, array $type, Context $context)
    {
        $this->setDomElement($data);
        if (!$this->domElement instanceof \DOMNode) {
            throw new RuntimeException('Wrong data given to visit.');
        }

        $data = (int) $this->getCurrentText();
        if (null === $this->result) {
            $this->result = $data;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitDouble($data, array $type, Context $context)
    {
        $this->setDomElement($data);
        if (!$this->domElement instanceof \DOMNode) {
            throw new RuntimeException('DomNode given for visitInteger was wrong');
        }

        $data = (double) $this->getCurrentText();
        if (null === $this->result) {
            $this->result = $data;
        }
        return $data;
    }

    /**
     * @param mixed $data
     * @param array $type
     *
     * @param Context $context
     * @throws Exception\RuntimeException
     * @return mixed
     */
    public function visitFloat($data, array $type, Context $context)
    {
        $this->setDomElement($data);
        if (!$this->domElement instanceof \DOMNode) {
            throw new RuntimeException('DomNode given for visitInteger was wrong');
        }

        $data = (float) $this->getCurrentText();
        if (null === $this->result) {
            $this->result = $data;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray($data, array $type, Context $context)
    {
        $entryName = null !== $this->currentMetadata && $this->currentMetadata->xmlEntryName
            ? $this->currentMetadata->xmlEntryName : 'entry';

        //if there is no occurrence of the node name inside of the data we will return an empty array
        $this->setDomElement($data);
        $childNodes = $this->getCurrentChildNodes();
        $matchedNode = array_filter($childNodes, function ($node) use ($entryName) {
            if ($node instanceof \DOMText) {
                return false;
            }
            if ($node instanceof \DOMElement) {
                return $node->nodeName == $entryName || $node->localName == $entryName;
            }
            return false;
        });

        if (count($matchedNode) == 0) {
            if (null === $this->result) {
                return $this->result = array();
            }

            return array();
        }
        /** now we should have 1 or 2 params only */
        switch(count($type['params']))
        {
            case 0:
                throw new RuntimeException(
                    sprintf(
                        'The array type must be specified either as "array<T>", or "array<K,V>".'
                    )
                );
            case 1:
                return $this->visitArrayWithOneParam($matchedNode, $type, $context);
                break;
            case 2:
                return $this->visitArrayWithTwoParam($matchedNode, $type, $context);
                break;
            default:
                throw new LogicException(
                    sprintf(
                        'The array type does not support more than 2 parameters, but got %s.',
                        json_encode($type['params'])
                    )
                );
        }

    }

    /**
     * helper method for the visitArray() method
     * 
     * @param array $data
     * @param array $type
     * @param Context $context
     * @return array
     */
    private function visitArrayWithOneParam(array $data, array $type, Context $context)
    {
        $result = array();
        //bind the result
        if (null === $this->result) {
            $this->result = &$result;
        }

        foreach ($data as $node) {
            $result[] =$this->navigator->accept($node, $type['params'][0], $context);
        }
        return $result;
    }

    /**
     * helper method for the visitArray() method
     * 
     * @param array $data
     * @param array $type
     * @param Context $context
     * @return array
     * @throws Exception\RuntimeException
     */
    private function visitArrayWithTwoParam(array $data, array $type, Context $context)
    {
        if (null === $this->currentMetadata) {
            throw new RuntimeException('Maps are not supported on top-level without metadata.');
        }
        list($keyType, $entryType) = $type['params'];
        $result = array();
        foreach ($data as $node) {
            //todo[max] implement this, forgot it
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingObject(ClassMetadata $metadata, $object, array $type, Context $context)
    {
        $this->setCurrentObject($object);

        if (null === $this->result) {
            $this->result = $this->currentObject;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function visitProperty(PropertyMetadata $metadata, $data, Context $context)
    {
        //the name to look for is either the serialized name (if set) or the simple name of the property
        $name = $this->namingStrategy->translateName($metadata);
        $this->setDomElement($data);

        if (!$metadata->type) {
            throw new RuntimeException(
                sprintf(
                    'You must define a type for %s::$%s.',
                    $metadata->reflection->class,
                    $metadata->name
                )
            );
        }

        /** if the property was selected as an Attribute*/
        if ($metadata->xmlAttribute) {
            $this->visitPropertyAttribute($metadata, $name, $context);
            return;
        }

        /** if the user wants to get the value selected by the type */
        if ($metadata->xmlValue) {
            $this->visitPropertyValue($metadata, $name, $context);
            return;
        }

        /** if the collection was selected as xml-annotation */
        if ($metadata->xmlCollection) {
            $this->visitPropertyCollection($metadata, $name, $context);
            return;
        }

        /** if nothing was selected take the default route*/
        $this->visitPropertyDefault($metadata, $name, $context);
    }


    /**
     * helper for the visitProperty() method to parse for attributes in the current node
     *
     * @param PropertyMetadata $metadata
     * @param $name
     * @param Context $context
     * @return null
     */
    private function visitPropertyAttribute(PropertyMetadata $metadata, $name, Context $context)
    {
        $attributes = $this->getCurrentAttributes();
        if (count($attributes) == 0) {
            $metadata->reflection->setValue($this->currentObject, null);
            return null;
        }
        $attributeNode = array_filter($attributes, function ($node) use ($name) {
            return $node instanceof \DOMAttr && $node->localName == $name;
        });

        //get the first and hopefully only one
        $attributeNode = array_shift($attributeNode);
        $v = $this->navigator->accept(
            $attributeNode,
            $metadata->type,
            $context
        );
        $metadata->reflection->setValue($this->currentObject, $v);
        return;
    }

    /**
     * if the type of the property is "@xmlValue" we wants to get the complete value of the current node
     * inside of this property. This content could have a normal type like string, integer, ... or an object again
     *
     *
     * @param PropertyMetadata $metadata
     * @param $name
     * @param Context $context
     * @throws Exception\RuntimeException
     * @return null
     */
    private function visitPropertyValue(PropertyMetadata $metadata, $name, Context $context)
    {
        if ($this->domElement->localName != $name) {
            throw new RuntimeException(
                sprintf(
                    'The current element`s name %s does not match with the given one %s',
                    $this->domElement->localName,
                    $name
                )
            );
        }

        $v = $this->navigator->accept(
            $this->domElement,
            $metadata->type,
            $context
        );

        //insert the result into the reflection
        $metadata->reflection->setValue($this->currentObject, $v);
        return;
    }


    /**
     * will fill its data into a collection
     *
     * @param PropertyMetadata $metadata
     * @param $name
     * @param Context $context
     */
    private function visitPropertyCollection(PropertyMetadata $metadata, $name, Context $context)
    {
        /**@var \DOMNodeList */
        $enclosingElem = $this->domElement;
        $nodeWithName = $this->domElement->getElementsByTagName($name);
        if (!$metadata->xmlCollectionInline && $nodeWithName->length == 1) {
            $enclosingElem = $nodeWithName->item(0);
        }
        $this->setCurrentMetadata($metadata);
        $v = $this->navigator->accept($enclosingElem, $metadata->type, $context);
        $this->revertCurrentMetadata();
        $metadata->reflection->setValue($this->currentObject, $v);
    }

    /**
     *
     * @param PropertyMetadata $metadata
     * @param $name
     * @param Context $context
     * @throws Exception\RuntimeException
     * @return null
     */
    private function visitPropertyDefault(PropertyMetadata $metadata, $name, Context $context)
    {
        $nodeList = $this->domElement->getElementsByTagName($name);

        //no node found with that name
        if ($nodeList->length == 0) {
            throw new RuntimeException(
                sprintf(
                    'There is no Tag with the name %s',
                    $name
                )
            );
        }

        //look for the node with that name
        $childNode = null;
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            if ($node instanceof \DOMNode) {
                $localName = $node->localName;
                if ($localName == $name) {
                    $childNode = $node;
                    continue;
                }
            }
        }
        if ($childNode == null) {
            return null;
        }
        $v = $this->navigator->accept(
            $childNode,
            $metadata->type,
            $context
        );
        if (null == $metadata->setter) {
            $metadata->reflection->setValue($this->currentObject, $v);
            return;
        }
        $this->currentObject->{$metadata->setter}($v);
    }

    /**
     * {@inheritdoc}
     */
    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        $rs = $this->currentObject;
        $this->revertCurrentObject();
        return $rs;
    }


    /**
     * @param mixed $object
     */
    public function setCurrentObject($object)
    {
        $this->objectStack->push($this->currentObject);
        $this->currentObject = $object;
    }

    /**
     * @return mixed
     */
    public function getCurrentObject()
    {
        return $this->currentObject;
    }

    /**
     * @return mixed
     */
    public function revertCurrentObject()
    {
        return $this->currentObject = $this->objectStack->pop();
    }

    /**
     * @param PropertyMetadata $metadata
     */
    public function setCurrentMetadata(PropertyMetadata $metadata)
    {
        $this->metadataStack->push($this->currentMetadata);
        $this->currentMetadata = $metadata;
    }

    /**
     * @return PropertyMetadata
     */
    public function getCurrentMetadata()
    {
        return $this->currentMetadata;
    }

    /**
     * @return PropertyMetadata
     */
    public function revertCurrentMetadata()
    {
        return $this->currentMetadata = $this->metadataStack->pop();
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array<string> $doctypeWhitelist
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
     * this little method will check a node for a node null value
     * this could be if nil="true" or the string content of the node is NULL or null
     * in both cases we will return true and set the current
     *
     * @param $data
     * @return bool
     */
    public function checkNullNode($data)
    {
        $this->setDomElement($data);
        //check if there is an nil attribute
        $nodeAttributes = $this->getCurrentAttributes();
        foreach ($nodeAttributes as $nodeAttribute) {
            if ($nodeAttribute->localName == 'nil' && $nodeAttribute->textContent == "true") {
                return true;
            }
        }

        //check if the textContent is NULL or null
        if ($this->getCurrentText() == 'NULL' || $this->getCurrentText() == 'null') {
            return true;
        }
        return false;
    }

    /**
     * What should happen if a value, attribute, .. matches to a node that is a null node:
     * < ... ns:nil="true"> or <result>null</result>
     * In this case we will set the property to null
     *
     * @param \Metadata\PropertyMetadata $metadata
     * @param $data
     * @param Context $context* @param Context $context
     */
    private function visitNullNode(\Metadata\PropertyMetadata $metadata, $data, Context $context)
    {
        $metadata->reflection->setValue($this->currentObject, null);
        return;
    }
}
