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
     * @var string
     */
    public $sinceVersion;
    /**
     * @var string
     */
    public $untilVersion;
    /**
     * @var string[]
     */
    public $groups;
    /**
     * @var string
     */
    public $serializedName;
    /**
     * @var array
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
     * @var string
     */
    public $xmlEntryName;

    /**
     * @var string
     */
    public $xmlEntryNamespace;

    /**
     * @var string
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
     * @var string
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
     * @var string
     */
    public $getter;

    /**
     * @var string
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
     * @var string|Expression
     */
    public $excludeIf = null;

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

    /**
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
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
        $parentStr = $this->unserializeProperties($str);
        parent::unserialize($parentStr);
    }

    protected function unserializeProperties(string $str): string
    {
        $unserialized = unserialize($str);
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
            $parentStr,
        ] = $unserialized;

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

        return $parentStr;
    }
}
