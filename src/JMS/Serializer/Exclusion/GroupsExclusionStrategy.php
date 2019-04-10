<?php

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

class GroupsExclusionStrategy implements ExclusionStrategyInterface
{
    const DEFAULT_GROUP = 'Default';

    private $groups = array();
    private $nestedGroups = false;

    public function __construct(array $groups)
    {
        if (empty($groups)) {
            $groups = array(self::DEFAULT_GROUP);
        }

        foreach ($groups as $group) {
            if (is_array($group)) {
                $this->nestedGroups = true;
                break;
            }
        }

        if ($this->nestedGroups) {
            $this->groups = $groups;
        } else {
            foreach ($groups as $group) {
                $this->groups[$group] = true;
            }
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
        if ($this->nestedGroups) {
            $groups = $this->getGroupsFor($navigatorContext);

            if (!$property->groups) {
                return !in_array(self::DEFAULT_GROUP, $groups);
            }

            return $this->shouldSkipUsingGroups($property, $groups);
        } else {

            if (!$property->groups) {
                return !isset($this->groups[self::DEFAULT_GROUP]);
            }

            foreach ($property->groups as $group) {
                if (isset($this->groups[$group])) {
                    return false;
                }
            }
            return true;
        }
    }

    private function shouldSkipUsingGroups(PropertyMetadata $property, $groups)
    {
        foreach ($property->groups as $group) {
            if (in_array($group, $groups)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Context $navigatorContext
     * @return array
     */
    public function getGroupsFor(Context $navigatorContext)
    {
        if (!$this->nestedGroups) {
            return array_keys($this->groups);
        }

        $paths = $navigatorContext->getCurrentPath();

        $groups = $this->groups;
        foreach ($paths as $index => $path) {
            if (!array_key_exists($path, $groups)) {
                if ($index > 0) {
                    $groups = array(self::DEFAULT_GROUP);
                }

                break;
            }

            $groups = $groups[$path];

            if (!array_filter($groups, 'is_string')) {
                $groups += array(self::DEFAULT_GROUP);
            }
        }

        return $groups;
    }
}
