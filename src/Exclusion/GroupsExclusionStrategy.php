<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

final class GroupsExclusionStrategy implements ExclusionStrategyInterface
{
    public const DEFAULT_GROUP = 'Default';

    private $groups;

    /**
     * @var array<string,array<string,boolean>>
     */
    private $parsedGroups = [];

    /**
     * @var array<string,boolean>>
     */
    private $rootGroups = [];

    /**
     * @var bool
     */
    private $nestedGroups = false;

    public function __construct(array $groups)
    {
        $this->groups = $groups;
        $this->prepare($groups, '');
        $this->parsedGroups['.'] = $this->parsedGroups[''];
        unset($this->parsedGroups['']);
        $this->rootGroups = $this->parsedGroups['.'];
    }

    private function prepare(array $groups, $path)
    {
        $currentGroups = [];
        foreach ($groups as $key => $value) {
            if (is_string($key) && is_array($value)) {
                $this->nestedGroups = true;
                $this->prepare($value, $path . '.' . $key);
                continue;
            }

            $currentGroups[$value] = true;
        }

        if (empty($currentGroups)) {
            $currentGroups[self::DEFAULT_GROUP] = true;
        }

        $this->parsedGroups[$path] = $currentGroups;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext): bool
    {
        if (!$this->nestedGroups) {
            if(!$property->groups) {
                return !isset($this->rootGroups[self::DEFAULT_GROUP]);
            }

            foreach ($property->groups as $group) {
                if (isset($this->rootGroups[$group])) {
                    return false;
                }
            }
            return true;
        } else {
            $groups = $property->groups ?: [self::DEFAULT_GROUP];
            $path = $this->buildPathFromContext($navigatorContext);
            if(!isset($this->parsedGroups[$path])) {
                // If we reach that path it's because we were allowed so we fallback on default group
                $this->parsedGroups[$path] = [self::DEFAULT_GROUP => true];
            }
        }

        $againstGroups = $this->parsedGroups[$path];

        foreach ($groups as $group) {
            if (isset($againstGroups[$group])) {
                return false;
            }
        }

        return true;
    }

    private function buildPathFromContext(Context $navigatorContext): string
    {
        return '.' . implode('.', $navigatorContext->getCurrentPath());
    }

    /**
     * @deprecated 
     */
    public function getGroupsFor(Context $navigatorContext): array
    {
        if (!$this->nestedGroups) {
            return array_keys($this->rootGroups);
        }

        $paths = $navigatorContext->getCurrentPath();
        $groups = $this->groups;
        foreach ($paths as $index => $path) {
            if (!array_key_exists($path, $groups)) {
                if ($index > 0) {
                    $groups = [self::DEFAULT_GROUP];
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
