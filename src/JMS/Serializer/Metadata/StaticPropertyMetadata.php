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
            $this->value,
            parent::serialize()
        ));
    }

    public function unserialize($str)
    {
        $parentStr = $this->unserializeProperties($str);
        list($this->class, $this->name) = unserialize($parentStr);
    }

    protected function unserializeProperties($str)
    {
        list(
            $this->value,
            $parentStr
            ) = unserialize($str);
        return parent::unserializeProperties($parentStr);
    }
}
