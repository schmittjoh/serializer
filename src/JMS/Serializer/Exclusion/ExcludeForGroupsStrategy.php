<?php

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Context;

class ExcludeForGroupsStrategy implements ExclusionStrategyInterface
{
    private $serializationGroups = array();

    public function __construct(array $serializationGroups)
    {
        foreach ($serializationGroups as $group) {
            $this->serializationGroups[$group] = true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext)
    {
        if ( ! $property->excludeForGroups) {
            return false;
        }

        foreach ($property->excludeForGroups as $group) {
            if (isset($this->serializationGroups[$group])) {
                return true;
            }
        }

        return false;
    }
}