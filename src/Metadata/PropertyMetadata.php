<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata;

use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Expression\Expression;
use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    public const ACCESS_TYPE_PROPERTY = 'property';
    public const ACCESS_TYPE_PUBLIC_METHOD = 'public_method';

    /**
     * @var string|null
     */
    public $sinceVersion;

    /**
     * @var string|null
     */
    public $untilVersion;

    /**
     * @var string[]|null
     */
    public $groups;

    /**
     * @var string|null
     */
    public $serializedName;

    /**
     * @var array|null
     */
    public $type;

    /**
     * @var bool
     */
    public $xmlCollection = false;

    /**
     * @var bool
     */
    public $xmlCollectionInline = false;

    /**
     * @var bool
     */
    public $xmlCollectionSkipWhenEmpty = true;

    /**
     * @var string|null
     */
    public $xmlEntryName;

    /**
     * @var string|null
     */
    public $xmlEntryNamespace;

    /**
     * @var string|null
     */
    public $xmlKeyAttribute;

    /**
     * @var bool
     */
    public $xmlAttribute = false;

    /**
     * @var bool
     */
    public $xmlValue = false;

    /**
     * @var string|null
     */
    public $xmlNamespace;

    /**
     * @var bool
     */
    public $xmlKeyValuePairs = false;

    /**
     * @var bool
     */
    public $xmlElementCData = true;

    /**
     * @var string|null
     */
    public $getter;

    /**
     * @var string|null
     */
    public $setter;

    /**
     * @var bool
     */
    public $inline = false;

    /**
     * @var bool
     */
    public $skipWhenEmpty = false;

    /**
     * @var bool
     */
    public $readOnly = false;

    /**
     * @var bool
     */
    public $xmlAttributeMap = false;

    /**
     * @var int|null
     */
    public $maxDepth = null;

    /**
     * @var string|Expression|null
     */
    public $excludeIf = null;

    /**
     * @var bool|null
     */
    public $hasDefault;

    /**
     * @var mixed|null
     */
    public $defaultValue;

    /**
     * @internal
     *
     * @var bool
     */
    public $forceReflectionAccess = false;

    public function __construct(string $class, string $name)
    {
        parent::__construct($class, $name);

        try {
            $class = $this->getReflection()->getDeclaringClass();
            $this->forceReflectionAccess = $class->isInternal() || $class->getProperty($name)->isStatic();
        } catch (\ReflectionException $e) {
        }
    }

    private function getReflection(): \ReflectionProperty
    {
        return new \ReflectionProperty($this->class, $this->name);
    }

    public function setAccessor(string $type, ?string $getter = null, ?string $setter = null): void
    {
        if (self::ACCESS_TYPE_PUBLIC_METHOD === $type) {
            $class = $this->getReflection()->getDeclaringClass();

            if (empty($getter)) {
                if ($class->hasMethod('get' . $this->name) && $class->getMethod('get' . $this->name)->isPublic()) {
                    $getter = 'get' . $this->name;
                } elseif ($class->hasMethod('is' . $this->name) && $class->getMethod('is' . $this->name)->isPublic()) {
                    $getter = 'is' . $this->name;
                } elseif ($class->hasMethod('has' . $this->name) && $class->getMethod('has' . $this->name)->isPublic()) {
                    $getter = 'has' . $this->name;
                } else {
                    throw new InvalidMetadataException(sprintf('There is neither a public %s method, nor a public %s method, nor a public %s method in class %s. Please specify which public method should be used for retrieving the value of the property %s.', 'get' . ucfirst($this->name), 'is' . ucfirst($this->name), 'has' . ucfirst($this->name), $this->class, $this->name));
                }
            }

            if (empty($setter) && !$this->readOnly) {
                if ($class->hasMethod('set' . $this->name) && $class->getMethod('set' . $this->name)->isPublic()) {
                    $setter = 'set' . $this->name;
                } else {
                    throw new InvalidMetadataException(sprintf('There is no public %s method in class %s. Please specify which public method should be used for setting the value of the property %s.', 'set' . ucfirst($this->name), $this->class, $this->name));
                }
            }
        }

        $this->getter = $getter;
        $this->setter = $setter;
    }

    public function setType(array $type): void
    {
        $this->type = $type;
    }

    public static function isCollectionList(?array $type = null): bool
    {
        return is_array($type)
            && 'array' === $type['name']
            && isset($type['params'][0])
            && !isset($type['params'][1]);
    }

    public static function isCollectionMap(?array $type = null): bool
    {
        return is_array($type)
            && 'array' === $type['name']
            && isset($type['params'][0])
            && isset($type['params'][1]);
    }

    protected function serializeToArray(): array
    {
        return [
            $this->sinceVersion,
            $this->untilVersion,
            $this->groups,
            $this->serializedName,
            $this->type,
            $this->xmlCollection,
            $this->xmlCollectionInline,
            $this->xmlEntryName,
            $this->xmlKeyAttribute,
            $this->xmlAttribute,
            $this->xmlValue,
            $this->xmlNamespace,
            $this->xmlKeyValuePairs,
            $this->xmlElementCData,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->xmlAttributeMap,
            $this->maxDepth,
            $this->xmlEntryNamespace,
            $this->xmlCollectionSkipWhenEmpty,
            $this->excludeIf,
            $this->skipWhenEmpty,
            $this->forceReflectionAccess,
            $this->hasDefault,
            $this->defaultValue,
            parent::serializeToArray(),
        ];
    }

    protected function unserializeFromArray(array $data): void
    {
        [
            $this->sinceVersion,
            $this->untilVersion,
            $this->groups,
            $this->serializedName,
            $this->type,
            $this->xmlCollection,
            $this->xmlCollectionInline,
            $this->xmlEntryName,
            $this->xmlKeyAttribute,
            $this->xmlAttribute,
            $this->xmlValue,
            $this->xmlNamespace,
            $this->xmlKeyValuePairs,
            $this->xmlElementCData,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->xmlAttributeMap,
            $this->maxDepth,
            $this->xmlEntryNamespace,
            $this->xmlCollectionSkipWhenEmpty,
            $this->excludeIf,
            $this->skipWhenEmpty,
            $this->forceReflectionAccess,
            $this->hasDefault,
            $this->defaultValue,
            $parentData,
        ] = $data;

        parent::unserializeFromArray($parentData);
    }
}
