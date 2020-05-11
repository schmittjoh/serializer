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
     * @var array<string,array<string,boolean>>
     */
    private $parsedGroups = [];

    /**
     * @var bool
     */
    private $nestedGroups = false;

    public function __construct(array $groups)
    {
        $this->prepare($groups, 'root');
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
        $groups = $property->groups ?: [self::DEFAULT_GROUP];

        if (!$this->nestedGroups) {
            // Group are not nested so we
            $path = 'root';
        } else {
            $path = $this->buildPathFromContext($navigatorContext);
        }

        if(!isset($this->parsedGroups[$path])) {
            // If we reach that path it's because we were allowed so we fallback on default group
            $this->parsedGroups[$path] = [self::DEFAULT_GROUP => true];
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
        $path = $navigatorContext->getCurrentPath();
        array_unshift($path, 'root');
        return implode('.', $path);
    }

    public function getGroupsFor(Context $navigatorContext): array
    {
        if (!$this->nestedGroups) {
            return array_keys($this->parsedGroups['root']);
        }

        $path = $this->buildPathFromContext($navigatorContext);

        if (!isset($this->parsedGroups[$path])) {
            return [self::DEFAULT_GROUP];
        }

        return array_keys($this->parsedGroups[$path]);
    }
}
