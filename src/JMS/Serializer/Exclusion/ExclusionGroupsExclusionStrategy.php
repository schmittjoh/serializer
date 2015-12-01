<?php

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

class ExclusionGroupsExclusionStrategy implements ExclusionStrategyInterface
{
    private $groups = array();

    public function __construct(array $groups)
    {
        foreach ($groups as $group) {
            $this->groups[$group] = true;
        }
    }

    /**
     * Whether the class should be skipped.
     *
     * @param ClassMetadata $classMetadata
     *
     * @param Context $context
     * @return bool
     */
    public function shouldSkipClass(ClassMetadata $classMetadata, Context $context)
    {
        return $this->shouldSkipGroup($classMetadata->exclusionGroups);
    }

    /**
     * Whether the property should be skipped.
     *
     * @param PropertyMetadata $propertyMetadata
     *
     * @param Context $context
     * @return bool
     */
    public function shouldSkipProperty(PropertyMetadata $propertyMetadata, Context $context)
    {
        return $this->shouldSkipGroup($propertyMetadata->exclusionGroups);
    }

    /**
     * @param $metadataGroups
     * @return bool
     */
    private function shouldSkipGroup($metadataGroups) {
        if (false === $metadataGroups) {
            return false;
        }
        if (is_array($metadataGroups) && empty($metadataGroups)) {
            return true;
        }
        if (!empty($this->groups) && array_intersect(array_keys($this->groups), $metadataGroups)) {
            return true;
        }

        return false;
    }
}