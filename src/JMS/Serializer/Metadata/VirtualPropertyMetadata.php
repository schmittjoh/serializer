<?php

namespace JMS\Serializer\Metadata;

class VirtualPropertyMetadata extends PropertyMetadata
{
    public function __construct($class, $methodName)
    {
        if (0 === strpos($methodName, 'get')) {
            $fieldName = lcfirst(substr($methodName, 3));
        } else {
            $fieldName = $methodName;
        }

        $this->class = $class;
        $this->name = $fieldName;
        $this->getter = $methodName;
        $this->readOnly = true;
    }

    public function setValue($obj, $value)
    {
        throw new \LogicException('VirtualPropertyMetadata is immutable.');
    }

    public function setAccessor($type, $getter = null, $setter = null)
    {
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
            $this->xmlAttributeMap,
            $this->maxDepth,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->class,
            $this->name,
            'excludeIf' => $this->excludeIf,
        ));
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
            $this->xmlAttributeMap,
            $this->maxDepth,
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->class,
            $this->name
            ) = $unserialized;

        if (isset($unserialized['excludeIf'])) {
            $this->excludeIf = $unserialized['excludeIf'];
        }
    }
}
