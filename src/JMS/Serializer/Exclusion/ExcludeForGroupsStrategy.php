<?php

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Context;

class ExcludeForGroupsStrategy
{
    private $groups = array();

    public function __construct(array $groups)
    {
        foreach ($groups as $group) {
            $this->groups[$group] = true;
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
        if ( ! $property->groups) {
            return false;
        }

        foreach ($property->groups as $group) {
            if (isset($this->groups[$group])) {
                return true;
            }
        }

        return false;
    }
}