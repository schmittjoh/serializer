<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata;

use function serialize;
use function unserialize;

class StaticPropertyMetadata extends PropertyMetadata
{
    private $value;

    public function __construct(string $className, string $fieldName, $fieldValue, array $groups = [])
    {
        $this->class          = $className;
        $this->name           = $fieldName;
        $this->serializedName = $fieldName;
        $this->value          = $fieldValue;
        $this->readOnly       = true;
        $this->groups         = $groups;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setAccessor(string $type, ?string $getter = null, ?string $setter = null): void
    {
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
            $this->class,
            $this->name,
            $this->value,
        ]);
    }

    public function unserialize($str): void
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
