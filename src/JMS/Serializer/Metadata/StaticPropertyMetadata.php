<?php

namespace JMS\Serializer\Metadata;

class StaticPropertyMetadata extends PropertyMetadata
{
    private $value;

    public function __construct($className, $fieldName, $fieldValue, array $groups = array())
    {
        $this->class = $className;
        $this->name = $fieldName;
        $this->value = $fieldValue;
        $this->readOnly = true;
        $this->groups = $groups;
    }

    public function getValue($obj)
    {
        return $this->value;
    }

    public function setValue($obj, $value)
    {
        throw new \LogicException('StaticPropertyMetadata is immutable.');
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
            $this->getter,
            $this->setter,
            $this->inline,
            $this->readOnly,
            $this->class,
            $this->name,
            $this->value
        ));
    }

    public function unserialize($str)
    {
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
            $this->class,
            $this->name,
            $this->value
            ) = unserialize($str);
    }
}
