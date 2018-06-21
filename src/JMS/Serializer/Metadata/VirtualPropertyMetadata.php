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

    public function unserialize($str)
    {
        $parentStr = $this->unserializeProperties($str);
        list($this->class, $this->name) = unserialize($parentStr);
    }
}
