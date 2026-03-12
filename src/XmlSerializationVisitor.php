<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Type\Type;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * @phpstan-import-type TypeArray from Type
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
     * @var \DOMNode[]
     */
    private array $stack = [];

    /**
     * @var array<PropertyMetadata|null>
     */
    private array $metadataStack = [];

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
     * @var ClassMetadata[]
     */
    private array $objectMetadataStack = [];

    /**
     * @var ClassMetadata|null
     */
    private ?ClassMetadata $currentObjectMetadata = null;

    /**
     * Cached default namespace of the current object's class metadata.
     */
    private ?string $currentClassDefaultNamespace = null;

    /**
     * @var array<string, string>
     */
    private $namespacePrefixCache = [];
    /**
     * @var array<string, bool>
     */
    private $elementNameValidCache = [];

    public function __construct(
        bool $formatOutput = true,
        string $defaultEncoding = 'UTF-8',
        string $defaultVersion = '1.0',
        string $defaultRootName = 'result',
        ?string $defaultRootNamespace = null,
        ?string $defaultRootPrefix = null
    ) {
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
     * @param TypeArray $type
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
        $dataResult = $data;
        $precision = $type['params'][0] ?? null;
        if (is_int($precision)) {
            $roundMode = $type['params'][1] ?? null;
            $roundMode = $this->mapRoundMode($roundMode);
            $dataResult = round($dataResult, $precision, $roundMode);
        }

        $decimalsNumbers = $type['params'][2] ?? null;
        if (null === $decimalsNumbers) {
            $parts = explode('.', (string) $dataResult);
            if (count($parts) < 2 || !$parts[1]) {
                $decimalsNumbers = 1;
            }
        }

        if (null !== $decimalsNumbers) {
            $dataResult = number_format($dataResult, $decimalsNumbers, '.', '');
        }

        return $this->document->createTextNode((string) $dataResult);
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray(array $data, array $type): void
    {
        if (null === $this->currentNode) {
            $this->createRoot();
        }

        $cm = $this->currentMetadata;
        $entryName = null !== $cm && null !== $cm->xmlEntryName ? $cm->xmlEntryName : 'entry';
        $keyAttributeName = null !== $cm && null !== $cm->xmlKeyAttribute ? $cm->xmlKeyAttribute : null;
        $namespace = null !== $cm && null !== $cm->xmlEntryNamespace ? $cm->xmlEntryNamespace : null;
        $useKeyValuePairs = null !== $cm && $cm->xmlKeyValuePairs;

        $elType = $this->getElementType($type);
        $parentNode = $this->currentNode;

        foreach ($data as $k => $v) {
            $tagName = $useKeyValuePairs && $this->isElementNameValid((string) $k) ? $k : $entryName;

            $entryNode = null === $namespace ? $this->document->createElement($tagName) : $this->createElement($tagName, $namespace);
            $parentNode->appendChild($entryNode);
            $this->currentNode = $entryNode;

            if (null !== $keyAttributeName) {
                $entryNode->setAttribute($keyAttributeName, (string) $k);
            }

            try {
                if (null !== $node = $this->navigator->accept($v, $elType)) {
                    $entryNode->appendChild($node);
                }
            } catch (NotAcceptableException $e) {
                $parentNode->removeChild($entryNode);
            }

            $this->currentNode = $parentNode;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingObject(ClassMetadata $metadata, object $data, array $type): void
    {
        $this->objectMetadataStack[] = $metadata;
        $this->currentObjectMetadata = $metadata;
        $this->currentClassDefaultNamespace = $metadata->xmlNamespaces[''] ?? null;

        if (null === $this->currentNode) {
            $this->createRoot($metadata);
        }

        if ($metadata->xmlNamespaces) {
            $this->addNamespaceAttributes($metadata, $this->currentNode);
        }

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
                throw new RuntimeException(sprintf('Unsupported value for property %s::$%s. Expected character data, but got %s.', $metadata->class, $metadata->name, \is_object($node) ? \get_class($node) : \gettype($node)));
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

        if (str_contains($metadata->serializedName, '/@') && $this->trySerializePropertyAsAttributeOnSiblingElement($metadata, $v)) {
            return;
        }

        if ($metadata->serializedName[0] === '@') {
            [$attributeValue, $processedNode] = $this->processValueForXmlAttribute($v, $metadata->type, $metadata);

            if (null === $v && null === $processedNode) {
                return;
            }

            $attributeName = substr($metadata->serializedName, 1);

            if ($this->currentNode instanceof \DOMElement) {
                $this->setAttributeOnNode($this->currentNode, $attributeName, $attributeValue, $metadata->xmlNamespace);
            } else {
                throw new RuntimeException('Cannot set attribute on a non-element node.');
            }

            return;
        }

        if ($addEnclosingElement = !($metadata->xmlCollection && $metadata->xmlCollectionInline) && !$metadata->inline) {
            $ns = $metadata->xmlNamespace ?? $this->currentClassDefaultNamespace;
            $element = null === $ns ? $this->document->createElement($metadata->serializedName) : $this->createElement($metadata->serializedName, $ns);
            $this->currentNode->appendChild($element);
            $this->stack[] = $this->currentNode;
            $this->currentNode = $element;
        }

        $this->metadataStack[] = $this->currentMetadata;
        $this->currentMetadata = $metadata;

        try {
            if (null !== $node = $this->navigator->accept($v, $metadata->type)) {
                $this->currentNode->appendChild($node);
            }
        } catch (NotAcceptableException $e) {
            $this->currentNode->parentNode->removeChild($this->currentNode);
            $this->currentMetadata = array_pop($this->metadataStack);
            $this->currentNode = array_pop($this->stack);
            $this->hasValue = false;

            return;
        }

        $this->currentMetadata = array_pop($this->metadataStack);

        if ($addEnclosingElement) {
            $this->currentNode = array_pop($this->stack);

            if ($this->isElementEmpty($element) && (null === $v || $this->isSkippableCollection($metadata) || $this->isSkippableEmptyObject($node, $metadata))) {
                $this->currentNode->removeChild($element);
            }
        }

        $this->hasValue = false;
    }

    private function trySerializePropertyAsAttributeOnSiblingElement(PropertyMetadata $metadata, $v): bool
    {
        [$elementName, $attributeName] = explode('/@', $metadata->serializedName, 2);
        $namespace = $metadata->xmlNamespace ?? $this->currentClassDefaultNamespace;
        $targetElement = null;

        if ($this->currentNode instanceof \DOMElement) {
            foreach ($this->currentNode->childNodes as $childNode) {
                if ($childNode instanceof \DOMElement && $childNode->localName === $elementName) {
                    $isNamespaceMatch = false;
                    // Case 1: Expected a specific namespace, and child node has it.
                    // Case 2 (else): Expected no namespace
                    if (null !== $namespace && $childNode->namespaceURI === $namespace) {
                        $isNamespaceMatch = true;
                    } elseif ((null === $namespace || '' === $namespace) && (null === $childNode->namespaceURI || '' === $childNode->namespaceURI)) {
                        $isNamespaceMatch = true;
                    }

                    if ($isNamespaceMatch) {
                        $targetElement = $childNode;
                        break;
                    }
                }
            }
        }

        if (!$targetElement) {
            return false;
        }

        if (null === $v) {
            return true;
        }

        [$attributeStringValue, $attributeValueNode] = $this->processValueForXmlAttribute($v, $metadata->type, $metadata);

        if (null !== $attributeValueNode || is_scalar($v)) {
            $this->setAttributeOnNode($targetElement, $attributeName, $attributeStringValue, $metadata->xmlNamespace);
        }

        return true;
    }

    /**
     * @return array{0:string, 1:\DOMNode|null} string value for attribute, and the processed DOMNode/null.
     *
     * @throws RuntimeException If the value is unsuitable for an XML attribute.
     */
    private function processValueForXmlAttribute($inputValue, ?array $valueType, PropertyMetadata $metadataForNavigatorContext): array
    {
        $this->setCurrentMetadata($metadataForNavigatorContext);
        $processedNode = $this->navigator->accept($inputValue, $valueType);
        $this->revertCurrentMetadata();

        if ($processedNode instanceof \DOMCharacterData) {
            $stringValue = $processedNode->nodeValue;
        } elseif (null === $processedNode) {
            $stringValue = is_scalar($inputValue) ? (string) $inputValue : '';
        } else {
            throw new RuntimeException(sprintf(
                'Unsupported value for XML attribute for property "%s". Expected character data or scalar, but got %s.',
                $metadataForNavigatorContext->name,
                \is_object($processedNode) ? \get_class($processedNode) : \gettype($processedNode),
            ));
        }

        return [$stringValue, $processedNode];
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
        array_pop($this->objectMetadataStack);
        $n = \count($this->objectMetadataStack);
        if ($n > 0) {
            $this->currentObjectMetadata = $this->objectMetadataStack[$n - 1];
            $this->currentClassDefaultNamespace = $this->currentObjectMetadata->xmlNamespaces[''] ?? null;
        } else {
            $this->currentObjectMetadata = null;
            $this->currentClassDefaultNamespace = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResult($node)
    {
        $this->navigator = null;
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
                'http://www.w3.org/2001/XMLSchema-instance',
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
        $this->metadataStack[] = $this->currentMetadata;
        $this->currentMetadata = $metadata;
    }

    public function setCurrentNode(\DOMNode $node): void
    {
        $this->stack[] = $this->currentNode;
        $this->currentNode = $node;
    }

    public function setCurrentAndRootNode(\DOMNode $node): void
    {
        $this->setCurrentNode($node);
        $this->document->appendChild($node);
    }

    public function revertCurrentNode(): ?\DOMNode
    {
        return $this->currentNode = array_pop($this->stack);
    }

    public function revertCurrentMetadata(): ?PropertyMetadata
    {
        return $this->currentMetadata = array_pop($this->metadataStack);
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
        return $this->elementNameValidCache[$name] ?? $this->elementNameValidCache[$name] = $name && false === strpos($name, ' ') && preg_match('#^[\pL_][\pL0-9._-]*$#ui', $name);
    }

    private function resolveNamespacePrefix(string $namespace): string
    {
        if (!isset($this->namespacePrefixCache[$namespace])) {
            $this->namespacePrefixCache[$namespace] = 'ns-' . substr(sha1($namespace), 0, 8);
        }

        return $this->namespacePrefixCache[$namespace];
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
        if (null === $namespace) {
            return $this->document->createElement($tagName);
        }

        // See #1087 - element must be like: <element xmlns="" /> - https://www.w3.org/TR/REC-xml-names/#iri-use
        // Use of an empty string in a namespace declaration turns it into an "undeclaration".
        if ('' === $namespace) {
            // If we have a default namespace, we need to create namespaced.
            if ($this->parentHasNonEmptyDefaultNs()) {
                return $this->document->createElementNS($namespace, $tagName);
            }

            return $this->document->createElement($tagName);
        }

        if ($this->currentNode->isDefaultNamespace($namespace)) {
            return $this->document->createElementNS($namespace, $tagName);
        }

        if (!($prefix = $this->currentNode->lookupPrefix($namespace)) && !($prefix = $this->document->lookupPrefix($namespace))) {
            $prefix = $this->resolveNamespacePrefix($namespace);
        }

        return $this->document->createElementNS($namespace, $prefix . ':' . $tagName);
    }

    private function setAttributeOnNode(\DOMElement $node, string $name, string $value, ?string $namespace = null): void
    {
        if (null !== $namespace) {
            if (!$prefix = $node->lookupPrefix($namespace)) {
                $prefix = $this->resolveNamespacePrefix($namespace);
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
