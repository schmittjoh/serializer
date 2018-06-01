<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata;

use JMS\Serializer\Exception\InvalidMetadataException;
use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    const ACCESS_TYPE_PROPERTY = 'property';
    const ACCESS_TYPE_PUBLIC_METHOD = 'public_method';

    public $sinceVersion;
    public $untilVersion;
    public $groups;
    public $serializedName;
    public $type;
    public $xmlCollection = false;
    public $xmlCollectionInline = false;
    public $xmlCollectionSkipWhenEmpty = true;
    public $xmlEntryName;
    public $xmlEntryNamespace;
    public $xmlKeyAttribute;
    public $xmlAttribute = false;
    public $xmlValue = false;
    public $xmlNamespace;
    public $xmlKeyValuePairs = false;
    public $xmlElementCData = true;
    public $getter;
    public $setter;
    public $inline = false;
    public $skipWhenEmpty = false;
    public $readOnly = false;
    public $xmlAttributeMap = false;
    public $maxDepth = null;
    public $excludeIf = null;

    /**
     * @internal
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

    public function setAccessor(string $type, ?string $getter = null, ?string $setter = null):void
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

    public function setType(array $type)
    {
        $this->type = $type;
    }

    public static function isCollectionList(array $type = null): bool
    {
        return is_array($type)
            && $type['name'] === 'array'
            && isset($type['params'][0])
            && !isset($type['params'][1]);
    }

    public static function isCollectionMap(array $type = null): bool
    {
        return is_array($type)
            && $type['name'] === 'array'
            && isset($type['params'][0])
            && isset($type['params'][1]);
    }

    public function serialize()
    {
        return serialize([
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
            parent::serialize(),
            'xmlEntryNamespace' => $this->xmlEntryNamespace,
            'xmlCollectionSkipWhenEmpty' => $this->xmlCollectionSkipWhenEmpty,
            'excludeIf' => $this->excludeIf,
            'skipWhenEmpty' => $this->skipWhenEmpty,
            'forceReflectionAccess' => $this->forceReflectionAccess,
        ]);
    }

    public function unserialize($str)
    {
        $unserialized = unserialize($str);
        list(
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
            $parentStr
            ) = $unserialized;

        if (isset($unserialized['xmlEntryNamespace'])) {
            $this->xmlEntryNamespace = $unserialized['xmlEntryNamespace'];
        }
        if (isset($unserialized['xmlCollectionSkipWhenEmpty'])) {
            $this->xmlCollectionSkipWhenEmpty = $unserialized['xmlCollectionSkipWhenEmpty'];
        }
        if (isset($unserialized['excludeIf'])) {
            $this->excludeIf = $unserialized['excludeIf'];
        }
        if (isset($unserialized['skipWhenEmpty'])) {
            $this->skipWhenEmpty = $unserialized['skipWhenEmpty'];
        }
        if (isset($unserialized['forceReflectionAccess'])) {
            $this->forceReflectionAccess = $unserialized['forceReflectionAccess'];
        }

        parent::unserialize($parentStr);
    }
}
