<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Exclusion;

use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;

class GroupsExclusionStrategyTest extends TestCase
{
    /**
     * @param array $propertyGroups
     * @param array $groups
     * @param bool $exclude
     *
     * @dataProvider getExclusionRules
     */
    public function testUninitializedContextIsWorking(array $propertyGroups, array $groups, $exclude)
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'prop', 'propVal');
        $metadata->groups = $propertyGroups;

        $strat = new GroupsExclusionStrategy($groups);
        self::assertEquals($strat->shouldSkipProperty($metadata, SerializationContext::create()), $exclude);
    }

    public function getExclusionRules()
    {
        return [
            [['foo'], ['foo'], false],
            [['foo'], [], true],
            [[], ['foo'], true],
            [['foo'], ['bar'], true],
            [['bar'], ['foo'], true],

            [['foo', GroupsExclusionStrategy::DEFAULT_GROUP], [], false],
            [['foo', 'bar'], [], true],
            [['foo', 'bar'], [GroupsExclusionStrategy::DEFAULT_GROUP], true],
            [['foo', 'bar'], ['foo'], false],

            [['foo', GroupsExclusionStrategy::DEFAULT_GROUP], ['test'], true],
            [['foo', GroupsExclusionStrategy::DEFAULT_GROUP, 'test'], ['test'], false],

            [['foo'], [GroupsExclusionStrategy::DEFAULT_GROUP], true],
            [[GroupsExclusionStrategy::DEFAULT_GROUP], [], false],
            [[], [GroupsExclusionStrategy::DEFAULT_GROUP], false],
            [[GroupsExclusionStrategy::DEFAULT_GROUP], [GroupsExclusionStrategy::DEFAULT_GROUP], false],
            [[GroupsExclusionStrategy::DEFAULT_GROUP, 'foo'], [GroupsExclusionStrategy::DEFAULT_GROUP], false],
            [[GroupsExclusionStrategy::DEFAULT_GROUP], [GroupsExclusionStrategy::DEFAULT_GROUP, 'foo'], false],
            [['foo'], [GroupsExclusionStrategy::DEFAULT_GROUP, 'foo'], false],
        ];
    }
}
