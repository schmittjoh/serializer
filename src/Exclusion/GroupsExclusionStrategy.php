<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

final class GroupsExclusionStrategy implements ExclusionStrategyInterface
{
    public const DEFAULT_GROUP = 'Default';

    /**
     * @var array
     */
    private $groups = [];

    /**
     * @var bool
     */
    private $nestedGroups = false;

    public function __construct(array $groups)
    {
        if (empty($groups)) {
            $groups = [self::DEFAULT_GROUP];
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

    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext): bool
    {
        return false;
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext): bool
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
                if (is_scalar($group) && isset($this->groups[$group])) {
                    return false;
                }
            }

            return true;
        }
    }

    private function shouldSkipUsingGroups(PropertyMetadata $property, array $groups): bool
    {
        foreach ($property->groups as $group) {
            if (in_array($group, $groups)) {
                return false;
            }
        }

        return true;
    }

    public function getGroupsFor(Context $navigatorContext): array
    {
        if (!$this->nestedGroups) {
            return array_keys($this->groups);
        }

        $paths = $navigatorContext->getCurrentPath();
        $groups = $this->groups;
        foreach ($paths as $index => $path) {
            if (!array_key_exists($path, $groups)) {
                if ($index > 0) {
                    $groups = [self::DEFAULT_GROUP];
                } else {
                    $groups = array_filter($groups, 'is_string') ?: [self::DEFAULT_GROUP];
                }

                break;
            }

            $groups = $groups[$path];
            if (!array_filter($groups, 'is_string')) {
                $groups += [self::DEFAULT_GROUP];
            }
        }

        return $groups;
    }
}
