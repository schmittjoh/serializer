<?php

namespace JMS\Serializer\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;

class IdenticalPropertyNamingStrategy implements PropertyNamingStrategyInterface
{
    public function translateName(PropertyMetadata $property)
    {
        return $property->name;
    }
}
