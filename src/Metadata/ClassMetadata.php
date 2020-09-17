<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata;

use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Ordering\AlphabeticalPropertyOrderingStrategy;
use JMS\Serializer\Ordering\CustomPropertyOrderingStrategy;
use JMS\Serializer\Ordering\IdenticalPropertyOrderingStrategy;
use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;
use Metadata\MethodMetadata;
use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * Class Metadata used to customize the serialization process.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ClassMetadata extends MergeableClassMetadata
{
    public const ACCESSOR_ORDER_UNDEFINED = 'undefined';
    public const ACCESSOR_ORDER_ALPHABETICAL = 'alphabetical';
    public const ACCESSOR_ORDER_CUSTOM = 'custom';

    /** @var \ReflectionMethod[] */
    public $preSerializeMethods = [];

    /** @var \ReflectionMethod[] */
    public $postSerializeMethods = [];

    /** @var \ReflectionMethod[] */
    public $postDeserializeMethods = [];

    /**
     * @var string
     */
    public $xmlRootName;

    /**
     * @var string
     */
    public $xmlRootNamespace;

    /**
     * @var string
     */
    public $xmlRootPrefix;
    /**
     * @var string[]
     */
    public $xmlNamespaces = [];

    /**
     * @var string
     */
    public $accessorOrder;

    /**
     * @var string[]
     */
    public $customOrder;

    /**
     * @internal
     *
     * @var bool
     */
    public $usingExpression = false;

    /**
     * @internal
     *
     * @var bool
     */
    public $isList = false;

    /**
     * @internal
     *
     * @var bool
     */
    public $isMap = false;

    /**
     * @var bool
     */
    public $discriminatorDisabled = false;

    /**
     * @var string
     */
    public $discriminatorBaseClass;
    /**
     * @var string
     */
    public $discriminatorFieldName;
    /**
     * @var string
     */
    public $discriminatorValue;

    /**
     * @var string[]
     */
    public $discriminatorMap = [];

    /**
     * @var string[]
     */
    public $discriminatorGroups = [];

    /**
     * @var bool
     */
    public $xmlDiscriminatorAttribute = false;

    /**
     * @var bool
     */
    public $xmlDiscriminatorCData = true;

    /**
     * @var string
     */
    public $xmlDiscriminatorNamespace;

    /**
     * @var string|Expression
     */
    public $excludeIf;

    public function setDiscriminator(string $fieldName, array $map, array $groups = []): void
    {
        if (empty($fieldName)) {
            throw new InvalidMetadataException('The $fieldName cannot be empty.');
        }

        if (empty($map)) {
            throw new InvalidMetadataException('The discriminator map cannot be empty.');
        }

        $this->discriminatorBaseClass = $this->name;
        $this->discriminatorFieldName = $fieldName;
        $this->discriminatorMap = $map;
        $this->discriminatorGroups = $groups;

        $this->handleDiscriminatorProperty();
    }

    private function getReflection(): \ReflectionClass
    {
        return new \ReflectionClass($this->name);
    }

    /**
     * Sets the order of properties in the class.
     *
     * @param array $customOrder
     *
     * @throws InvalidMetadataException When the accessor order is not valid.
     * @throws InvalidMetadataException When the custom order is not valid.
     */
    public function setAccessorOrder(string $order, array $customOrder = []): void
    {
        if (!in_array($order, [self::ACCESSOR_ORDER_UNDEFINED, self::ACCESSOR_ORDER_ALPHABETICAL, self::ACCESSOR_ORDER_CUSTOM], true)) {
            throw new InvalidMetadataException(sprintf('The accessor order "%s" is invalid.', $order));
        }

        foreach ($customOrder as $name) {
            if (!\is_string($name)) {
                throw new InvalidMetadataException(sprintf('$customOrder is expected to be a list of strings, but got element of value %s.', json_encode($name)));
            }
        }

        $this->accessorOrder = $order;
        $this->customOrder = array_flip($customOrder);
        $this->sortProperties();
    }

    public function addPropertyMetadata(BasePropertyMetadata $metadata): void
    {
        parent::addPropertyMetadata($metadata);
        $this->sortProperties();
        if ($metadata instanceof PropertyMetadata && $metadata->excludeIf) {
            $this->usingExpression = true;
        }
    }

    public function addPreSerializeMethod(MethodMetadata $method): void
    {
        $this->preSerializeMethods[] = $method;
    }

    public function addPostSerializeMethod(MethodMetadata $method): void
    {
        $this->postSerializeMethods[] = $method;
    }

    public function addPostDeserializeMethod(MethodMetadata $method): void
    {
        $this->postDeserializeMethods[] = $method;
    }

    public function merge(MergeableInterface $object): void
    {
        if (!$object instanceof ClassMetadata) {
            throw new InvalidMetadataException('$object must be an instance of ClassMetadata.');
        }

        parent::merge($object);

        $this->preSerializeMethods = array_merge($this->preSerializeMethods, $object->preSerializeMethods);
        $this->postSerializeMethods = array_merge($this->postSerializeMethods, $object->postSerializeMethods);
        $this->postDeserializeMethods = array_merge($this->postDeserializeMethods, $object->postDeserializeMethods);
        $this->xmlRootName = $object->xmlRootName;
        $this->xmlRootNamespace = $object->xmlRootNamespace;
        if (null !== $object->excludeIf) {
            $this->excludeIf = $object->excludeIf;
        }

        $this->xmlNamespaces = array_merge($this->xmlNamespaces, $object->xmlNamespaces);

        if ($object->accessorOrder) {
            $this->accessorOrder = $object->accessorOrder;
            $this->customOrder = $object->customOrder;
        }

        if ($object->discriminatorFieldName && $this->discriminatorFieldName) {
            throw new InvalidMetadataException(sprintf(
                'The discriminator of class "%s" would overwrite the discriminator of the parent class "%s". Please define all possible sub-classes in the discriminator of %s.',
                $object->name,
                $this->discriminatorBaseClass,
                $this->discriminatorBaseClass
            ));
        } elseif (!$this->discriminatorFieldName && $object->discriminatorFieldName) {
            $this->discriminatorFieldName = $object->discriminatorFieldName;
            $this->discriminatorMap = $object->discriminatorMap;
        }

        if (null !== $object->discriminatorDisabled) {
            $this->discriminatorDisabled = $object->discriminatorDisabled;
        }

        if ($object->discriminatorMap) {
            $this->discriminatorFieldName = $object->discriminatorFieldName;
            $this->discriminatorMap = $object->discriminatorMap;
            $this->discriminatorBaseClass = $object->discriminatorBaseClass;
            $this->discriminatorGroups = $object->discriminatorGroups;
        }

        $this->handleDiscriminatorProperty();

        $this->sortProperties();
    }

    public function registerNamespace(string $uri, ?string $prefix = null): void
    {
        if (!\is_string($uri)) {
            throw new InvalidMetadataException(sprintf('$uri is expected to be a strings, but got value %s.', json_encode($uri)));
        }

        if (null !== $prefix) {
            if (!\is_string($prefix)) {
                throw new InvalidMetadataException(sprintf('$prefix is expected to be a strings, but got value %s.', json_encode($prefix)));
            }
        } else {
            $prefix = '';
        }

        $this->xmlNamespaces[$prefix] = $uri;
    }

    /**
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function serialize()
    {
        $this->sortProperties();

        return serialize([
            $this->preSerializeMethods,
            $this->postSerializeMethods,
            $this->postDeserializeMethods,
            $this->xmlRootName,
            $this->xmlRootNamespace,
            $this->xmlNamespaces,
            $this->accessorOrder,
            $this->customOrder,
            $this->discriminatorDisabled,
            $this->discriminatorBaseClass,
            $this->discriminatorFieldName,
            $this->discriminatorValue,
            $this->discriminatorMap,
            $this->discriminatorGroups,
            $this->excludeIf,
            parent::serialize(),
            'discriminatorGroups' => $this->discriminatorGroups,
            'xmlDiscriminatorAttribute' => $this->xmlDiscriminatorAttribute,
            'xmlDiscriminatorCData' => $this->xmlDiscriminatorCData,
            'usingExpression' => $this->usingExpression,
            'xmlDiscriminatorNamespace' => $this->xmlDiscriminatorNamespace,
            'xmlRootPrefix' => $this->xmlRootPrefix,
            'isList' => $this->isList,
            'isMap' => $this->isMap,
        ]);
    }

    /**
     * @param string $str
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function unserialize($str)
    {
        $unserialized = unserialize($str);

        [
            $this->preSerializeMethods,
            $this->postSerializeMethods,
            $this->postDeserializeMethods,
            $this->xmlRootName,
            $this->xmlRootNamespace,
            $this->xmlNamespaces,
            $this->accessorOrder,
            $this->customOrder,
            $this->discriminatorDisabled,
            $this->discriminatorBaseClass,
            $this->discriminatorFieldName,
            $this->discriminatorValue,
            $this->discriminatorMap,
            $this->discriminatorGroups,
            $this->excludeIf,
            $parentStr,
        ] = $unserialized;

        if (isset($unserialized['discriminatorGroups'])) {
            $this->discriminatorGroups = $unserialized['discriminatorGroups'];
        }

        if (isset($unserialized['usingExpression'])) {
            $this->usingExpression = $unserialized['usingExpression'];
        }

        if (isset($unserialized['xmlDiscriminatorAttribute'])) {
            $this->xmlDiscriminatorAttribute = $unserialized['xmlDiscriminatorAttribute'];
        }

        if (isset($unserialized['xmlDiscriminatorNamespace'])) {
            $this->xmlDiscriminatorNamespace = $unserialized['xmlDiscriminatorNamespace'];
        }

        if (isset($unserialized['xmlDiscriminatorCData'])) {
            $this->xmlDiscriminatorCData = $unserialized['xmlDiscriminatorCData'];
        }

        if (isset($unserialized['xmlRootPrefix'])) {
            $this->xmlRootPrefix = $unserialized['xmlRootPrefix'];
        }

        if (isset($unserialized['isList'])) {
            $this->isList = $unserialized['isList'];
        }

        if (isset($unserialized['isMap'])) {
            $this->isMap = $unserialized['isMap'];
        }

        parent::unserialize($parentStr);
    }

    private function handleDiscriminatorProperty(): void
    {
        if (
            $this->discriminatorMap
            && !$this->getReflection()->isAbstract()
            && !$this->getReflection()->isInterface()
        ) {
            if (false === $typeValue = array_search($this->name, $this->discriminatorMap, true)) {
                throw new InvalidMetadataException(sprintf(
                    'The sub-class "%s" is not listed in the discriminator of the base class "%s".',
                    $this->name,
                    $this->discriminatorBaseClass
                ));
            }

            $this->discriminatorValue = $typeValue;

            if (
                isset($this->propertyMetadata[$this->discriminatorFieldName])
                && !$this->propertyMetadata[$this->discriminatorFieldName] instanceof StaticPropertyMetadata
            ) {
                throw new InvalidMetadataException(sprintf(
                    'The discriminator field name "%s" of the base-class "%s" conflicts with a regular property of the sub-class "%s".',
                    $this->discriminatorFieldName,
                    $this->discriminatorBaseClass,
                    $this->name
                ));
            }

            $discriminatorProperty = new StaticPropertyMetadata(
                $this->name,
                $this->discriminatorFieldName,
                $typeValue,
                $this->discriminatorGroups
            );
            $discriminatorProperty->serializedName = $this->discriminatorFieldName;
            $discriminatorProperty->xmlAttribute = $this->xmlDiscriminatorAttribute;
            $discriminatorProperty->xmlElementCData = $this->xmlDiscriminatorCData;
            $discriminatorProperty->xmlNamespace = $this->xmlDiscriminatorNamespace;
            $this->propertyMetadata[$this->discriminatorFieldName] = $discriminatorProperty;
        }
    }

    private function sortProperties(): void
    {
        switch ($this->accessorOrder) {
            case self::ACCESSOR_ORDER_UNDEFINED:
                $this->propertyMetadata = (new IdenticalPropertyOrderingStrategy())->order($this->propertyMetadata);
                break;

            case self::ACCESSOR_ORDER_ALPHABETICAL:
                $this->propertyMetadata = (new AlphabeticalPropertyOrderingStrategy())->order($this->propertyMetadata);
                break;

            case self::ACCESSOR_ORDER_CUSTOM:
                $this->propertyMetadata = (new CustomPropertyOrderingStrategy($this->customOrder))->order($this->propertyMetadata);
                break;
        }
    }
}
