<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

/**
 * XmlSerializationVisitor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class XmlSerializationVisitor extends AbstractVisitor implements SerializationVisitorInterface
{
    /**
     * @var \DOMDocument
     */
    private $document;

    /**
     * @var string
     */
    private $defaultRootName;

    /**
     * @var string|null
     */
    private $defaultRootNamespace;

    /**
     * @var string|null
     */
    private $defaultRootPrefix;

    /**
     * @var \SplStack
     */
    private $stack;

    /**
     * @var \SplStack
     */
    private $metadataStack;

    /**
     * @var \DOMNode|\DOMElement|null
     */
    private $currentNode;

    /**
     * @var ClassMetadata|PropertyMetadata|null
     */
    private $currentMetadata;

    /**
     * @var bool
     */
    private $hasValue;

    /**
     * @var bool
     */
    private $nullWasVisited;

    /**
     * @var \SplStack
     */
    private $objectMetadataStack;

    public function __construct(
        bool $formatOutput = true,
        string $defaultEncoding = 'UTF-8',
        string $defaultVersion = '1.0',
        string $defaultRootName = 'result',
        ?string $defaultRootNamespace = null,
        ?string $defaultRootPrefix = null
    ) {
        $this->objectMetadataStack = new \SplStack();
        $this->stack = new \SplStack();
        $this->metadataStack = new \SplStack();

        $this->currentNode = null;
        $this->nullWasVisited = false;

        $this->document = $this->createDocument($formatOutput, $defaultVersion, $defaultEncoding);

        $this->defaultRootName = $defaultRootName;
        $this->defaultRootNamespace = $defaultRootNamespace;
        $this->defaultRootPrefix = $defaultRootPrefix;
    }

    private function createDocument(bool $formatOutput, string $defaultVersion, string $defaultEncoding): \DOMDocument
    {
        $document = new \DOMDocument($defaultVersion, $defaultEncoding);
        $document->formatOutput = $formatOutput;

        return $document;
    }

    public function createRoot(?ClassMetadata $metadata = null, ?string $rootName = null, ?string $rootNamespace = null, ?string $rootPrefix = null): \DOMElement
    {
        if (null !== $metadata && !empty($metadata->xmlRootName)) {
            $rootPrefix = $metadata->xmlRootPrefix;
            $rootName = $metadata->xmlRootName;
            $rootNamespace = $metadata->xmlRootNamespace ?: $this->getClassDefaultNamespace($metadata);
        } else {
            $rootName = $rootName ?: $this->defaultRootName;
            $rootNamespace = $rootNamespace ?: $this->defaultRootNamespace;
            $rootPrefix = $rootPrefix ?: $this->defaultRootPrefix;
        }

        $document = $this->getDocument();
        if ($rootNamespace) {
            $rootNode = $document->createElementNS($rootNamespace, (null !== $rootPrefix ? $rootPrefix . ':' : '') . $rootName);
        } else {
            $rootNode = $document->createElement($rootName);
        }

        $document->appendChild($rootNode);
        $this->setCurrentNode($rootNode);

        return $rootNode;
    }

    /**
     * {@inheritdoc}
     */
    public function visitNull($data, array $type)
    {
        $node = $this->document->createAttribute('xsi:nil');
        $node->value = 'true';
        $this->nullWasVisited = true;

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function visitString(string $data, array $type)
    {
        $doCData = null !== $this->currentMetadata ? $this->currentMetadata->xmlElementCData : true;

        return $doCData ? $this->document->createCDATASection($data) : $this->document->createTextNode((string) $data);
    }

    /**
     * @param mixed $data
     * @param array $type
     */
    public function visitSimpleString($data, array $type): \DOMText
    {
        return $this->document->createTextNode((string) $data);
    }

    /**
     * {@inheritdoc}
     */
    public function visitBoolean(bool $data, array $type)
    {
        return $this->document->createTextNode($data ? 'true' : 'false');
    }

    /**
     * {@inheritdoc}
     */
    public function visitInteger(int $data, array $type)
    {
        return $this->document->createTextNode((string) $data);
    }

    /**
     * {@inheritdoc}
     */
    public function visitDouble(float $data, array $type)
    {
        return $this->document->createTextNode(var_export((float) $data, true));
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray(array $data, array $type): void
    {
        if (null === $this->currentNode) {
            $this->createRoot();
        }

        $entryName = null !== $this->currentMetadata && null !== $this->currentMetadata->xmlEntryName ? $this->currentMetadata->xmlEntryName : 'entry';
        $keyAttributeName = null !== $this->currentMetadata && null !== $this->currentMetadata->xmlKeyAttribute ? $this->currentMetadata->xmlKeyAttribute : null;
        $namespace = null !== $this->currentMetadata && null !== $this->currentMetadata->xmlEntryNamespace ? $this->currentMetadata->xmlEntryNamespace : null;

        $elType = $this->getElementType($type);
        foreach ($data as $k => $v) {
            $tagName = null !== $this->currentMetadata && $this->currentMetadata->xmlKeyValuePairs && $this->isElementNameValid((string) $k) ? $k : $entryName;

            $entryNode = $this->createElement($tagName, $namespace);
            $this->currentNode->appendChild($entryNode);
            $this->setCurrentNode($entryNode);

            if (null !== $keyAttributeName) {
                $entryNode->setAttribute($keyAttributeName, (string) $k);
            }

            try {
                if (null !== $node = $this->navigator->accept($v, $elType)) {
                    $this->currentNode->appendChild($node);
                }
            } catch (NotAcceptableException $e) {
                $this->currentNode->parentNode->removeChild($this->currentNode);
            }

            $this->revertCurrentNode();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingObject(ClassMetadata $metadata, object $data, array $type): void
    {
        $this->objectMetadataStack->push($metadata);

        if (null === $this->currentNode) {
            $this->createRoot($metadata);
        }

        $this->addNamespaceAttributes($metadata, $this->currentNode);

        $this->hasValue = false;
    }

    /**
     * {@inheritdoc}
     */
    public function visitProperty(PropertyMetadata $metadata, $v): void
    {
        if ($metadata->xmlAttribute) {
            $this->setCurrentMetadata($metadata);
            $node = $this->navigator->accept($v, $metadata->type);
            $this->revertCurrentMetadata();

            if (!$node instanceof \DOMCharacterData) {
                throw new RuntimeException(sprintf('Unsupported value for XML attribute for %s. Expected character data, but got %s.', $metadata->name, json_encode($v)));
            }

            $this->setAttributeOnNode($this->currentNode, $metadata->serializedName, $node->nodeValue, $metadata->xmlNamespace);

            return;
        }

        if (
            ($metadata->xmlValue && $this->currentNode->childNodes->length > 0)
            || (!$metadata->xmlValue && $this->hasValue)
        ) {
            throw new RuntimeException(sprintf('If you make use of @XmlValue, all other properties in the class must have the @XmlAttribute annotation. Invalid usage detected in class %s.', $metadata->class));
        }

        if ($metadata->xmlValue) {
            $this->hasValue = true;

            $this->setCurrentMetadata($metadata);
            $node = $this->navigator->accept($v, $metadata->type);
            $this->revertCurrentMetadata();

            if (!$node instanceof \DOMCharacterData) {
                throw new RuntimeException(sprintf('Unsupported value for property %s::$%s. Expected character data, but got %s.', $metadata->reflection->class, $metadata->reflection->name, \is_object($node) ? \get_class($node) : \gettype($node)));
            }

            $this->currentNode->appendChild($node);

            return;
        }

        if ($metadata->xmlAttributeMap) {
            if (!\is_array($v)) {
                throw new RuntimeException(sprintf('Unsupported value type for XML attribute map. Expected array but got %s.', \gettype($v)));
            }

            foreach ($v as $key => $value) {
                $this->setCurrentMetadata($metadata);
                $node = $this->navigator->accept($value, null);
                $this->revertCurrentMetadata();

                if (!$node instanceof \DOMCharacterData) {
                    throw new RuntimeException(sprintf('Unsupported value for a XML attribute map value. Expected character data, but got %s.', json_encode($v)));
                }

                $this->setAttributeOnNode($this->currentNode, $key, $node->nodeValue, $metadata->xmlNamespace);
            }

            return;
        }

        if ($addEnclosingElement = !$this->isInLineCollection($metadata) && !$metadata->inline) {
            $namespace = null !== $metadata->xmlNamespace
                ? $metadata->xmlNamespace
                : $this->getClassDefaultNamespace($this->objectMetadataStack->top());

            $element = $this->createElement($metadata->serializedName, $namespace);
            $this->currentNode->appendChild($element);
            $this->setCurrentNode($element);
        }

        $this->setCurrentMetadata($metadata);

        try {
            if (null !== $node = $this->navigator->accept($v, $metadata->type)) {
                $this->currentNode->appendChild($node);
            }
        } catch (NotAcceptableException $e) {
            $this->currentNode->parentNode->removeChild($this->currentNode);
            $this->revertCurrentMetadata();
            $this->revertCurrentNode();
            $this->hasValue = false;

            return;
        }

        $this->revertCurrentMetadata();

        if ($addEnclosingElement) {
            $this->revertCurrentNode();

            if ($this->isElementEmpty($element) && (null === $v || $this->isSkippableCollection($metadata) || $this->isSkippableEmptyObject($node, $metadata))) {
                $this->currentNode->removeChild($element);
            }
        }

        $this->hasValue = false;
    }

    private function isInLineCollection(PropertyMetadata $metadata): bool
    {
        return $metadata->xmlCollection && $metadata->xmlCollectionInline;
    }

    private function isSkippableEmptyObject(?\DOMElement $node, PropertyMetadata $metadata): bool
    {
        return null === $node && !$metadata->xmlCollection && $metadata->skipWhenEmpty;
    }

    private function isSkippableCollection(PropertyMetadata $metadata): bool
    {
        return $metadata->xmlCollection && $metadata->xmlCollectionSkipWhenEmpty;
    }

    private function isElementEmpty(\DOMElement $element): bool
    {
        return !$element->hasChildNodes() && !$element->hasAttributes();
    }

    public function endVisitingObject(ClassMetadata $metadata, object $data, array $type): void
    {
        $this->objectMetadataStack->pop();
    }

    /**
     * {@inheritdoc}
     */
    public function getResult($node)
    {
        if (null === $this->document->documentElement) {
            if ($node instanceof \DOMElement) {
                $this->document->appendChild($node);
            } else {
                $this->createRoot();
                if ($node) {
                    $this->document->documentElement->appendChild($node);
                }
            }
        }

        if ($this->nullWasVisited) {
            $this->document->documentElement->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:xsi',
                'http://www.w3.org/2001/XMLSchema-instance'
            );
        }

        return $this->document->saveXML();
    }

    public function getCurrentNode(): ?\DOMNode
    {
        return $this->currentNode;
    }

    public function getCurrentMetadata(): ?PropertyMetadata
    {
        return $this->currentMetadata;
    }

    public function getDocument(): \DOMDocument
    {
        return $this->document;
    }

    public function setCurrentMetadata(PropertyMetadata $metadata): void
    {
        $this->metadataStack->push($this->currentMetadata);
        $this->currentMetadata = $metadata;
    }

    public function setCurrentNode(\DOMNode $node): void
    {
        $this->stack->push($this->currentNode);
        $this->currentNode = $node;
    }

    public function setCurrentAndRootNode(\DOMNode $node): void
    {
        $this->setCurrentNode($node);
        $this->document->appendChild($node);
    }

    public function revertCurrentNode(): ?\DOMNode
    {
        return $this->currentNode = $this->stack->pop();
    }

    public function revertCurrentMetadata(): ?PropertyMetadata
    {
        return $this->currentMetadata = $this->metadataStack->pop();
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($data)
    {
        $this->nullWasVisited = false;

        return $data;
    }

    /**
     * Checks that the name is a valid XML element name.
     */
    private function isElementNameValid(string $name): bool
    {
        return $name && false === strpos($name, ' ') && preg_match('#^[\pL_][\pL0-9._-]*$#ui', $name);
    }

    /**
     * Adds namespace attributes to the XML root element
     */
    private function addNamespaceAttributes(ClassMetadata $metadata, \DOMElement $element): void
    {
        foreach ($metadata->xmlNamespaces as $prefix => $uri) {
            $attribute = 'xmlns';
            if ('' !== $prefix) {
                $attribute .= ':' . $prefix;
            } elseif ($element->namespaceURI === $uri) {
                continue;
            }

            $element->setAttributeNS('http://www.w3.org/2000/xmlns/', $attribute, $uri);
        }
    }

    private function createElement(string $tagName, ?string $namespace = null): \DOMElement
    {
        // See #1087 - element must be like: <element xmlns="" /> - https://www.w3.org/TR/REC-xml-names/#iri-use
        // Use of an empty string in a namespace declaration turns it into an "undeclaration".
        if ('' === $namespace) {
            // If we have a default namespace, we need to create namespaced.
            if ($this->parentHasNonEmptyDefaultNs()) {
                return $this->document->createElementNS($namespace, $tagName);
            }

            return $this->document->createElement($tagName);
        }

        if (null === $namespace) {
            return $this->document->createElement($tagName);
        }

        if ($this->currentNode->isDefaultNamespace($namespace)) {
            return $this->document->createElementNS($namespace, $tagName);
        }

        if (!($prefix = $this->currentNode->lookupPrefix($namespace)) && !($prefix = $this->document->lookupPrefix($namespace))) {
            $prefix = 'ns-' . substr(sha1($namespace), 0, 8);
        }

        return $this->document->createElementNS($namespace, $prefix . ':' . $tagName);
    }

    private function setAttributeOnNode(\DOMElement $node, string $name, string $value, ?string $namespace = null): void
    {
        if (null !== $namespace) {
            if (!$prefix = $node->lookupPrefix($namespace)) {
                $prefix = 'ns-' . substr(sha1($namespace), 0, 8);
            }

            $node->setAttributeNS($namespace, $prefix . ':' . $name, $value);
        } else {
            $node->setAttribute($name, $value);
        }
    }

    private function getClassDefaultNamespace(ClassMetadata $metadata): ?string
    {
        return $metadata->xmlNamespaces[''] ?? null;
    }

    private function parentHasNonEmptyDefaultNs(): bool
    {
        return null !== ($uri = $this->currentNode->lookupNamespaceUri(null)) && ('' !== $uri);
    }
}
