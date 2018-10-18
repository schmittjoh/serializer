<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata;

class VirtualPropertyMetadata extends PropertyMetadata
{
    public function __construct(string $class, string $methodName)
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

    public function setAccessor(string $type, ?string $getter = null, ?string $setter = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        $parentStr = $this->unserializeProperties($str);
        [$this->class, $this->name] = unserialize($parentStr);
    }
}
