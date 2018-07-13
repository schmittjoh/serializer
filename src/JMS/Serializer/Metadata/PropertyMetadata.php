<?php

namespace JMS\Serializer\Metadata;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\TypeParser;
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

    private $closureAccessor;

    private static $typeParser;

    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
        $this->initAccessor();
    }

    private function initAccessor()
    {
        $classRef = $this->reflection->getDeclaringClass();
        if ($classRef->isInternal() || $classRef->getProperty($this->name)->isStatic()) {
            $this->closureAccessor = function ($o) {
                return $this->reflection->getValue($o);
            };
        } else {
            $this->closureAccessor = \Closure::bind(function ($o, $name) {
                return $o->$name;
            }, null, $this->reflection->class);
        }
    }

    public function setAccessor($type, $getter = null, $setter = null)
    {
        if (self::ACCESS_TYPE_PUBLIC_METHOD === $type) {
            $class = $this->reflection->getDeclaringClass();

            if (empty($getter)) {
                if ($class->hasMethod('get' . $this->name) && $class->getMethod('get' . $this->name)->isPublic()) {
                    $getter = 'get' . $this->name;
                } elseif ($class->hasMethod('is' . $this->name) && $class->getMethod('is' . $this->name)->isPublic()) {
                    $getter = 'is' . $this->name;
                } elseif ($class->hasMethod('has' . $this->name) && $class->getMethod('has' . $this->name)->isPublic()) {
                    $getter = 'has' . $this->name;
                } else {
                    throw new RuntimeException(sprintf('There is neither a public %s method, nor a public %s method, nor a public %s method in class %s. Please specify which public method should be used for retrieving the value of the property %s.', 'get' . ucfirst($this->name), 'is' . ucfirst($this->name), 'has' . ucfirst($this->name), $this->class, $this->name));
                }
            }

            if (empty($setter) && !$this->readOnly) {
                if ($class->hasMethod('set' . $this->name) && $class->getMethod('set' . $this->name)->isPublic()) {
                    $setter = 'set' . $this->name;
                } else {
                    throw new RuntimeException(sprintf('There is no public %s method in class %s. Please specify which public method should be used for setting the value of the property %s.', 'set' . ucfirst($this->name), $this->class, $this->name));
                }
            }
        }

        $this->getter = $getter;
        $this->setter = $setter;
    }

    public function getValue($obj)
    {
        if (null === $this->getter) {
            if (null !== $this->closureAccessor) {
                $accessor = $this->closureAccessor;
                return $accessor($obj, $this->name);
            }

            return parent::getValue($obj);
        }

        return $obj->{$this->getter}();
    }

    public function setValue($obj, $value)
    {
        if (null === $this->setter) {
            parent::setValue($obj, $value);
            return;
        }

        $obj->{$this->setter}($value);
    }

    public function setType($type)
    {
        if (null === self::$typeParser) {
            self::$typeParser = new TypeParser();
        }

        $this->type = self::$typeParser->parse($type);
    }

    public function serialize()
    {
        return serialize(array(
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
        ));
    }

    public function unserialize($str)
    {
        $parentStr = $this->unserializeProperties($str);
        parent::unserialize($parentStr);

        $this->initAccessor();
    }

    protected function unserializeProperties($str)
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

        return $parentStr;
    }
}
