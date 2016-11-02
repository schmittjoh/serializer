<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests\Exclusion;

use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;

class GroupsExclusionStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getExclusionRules
     * @param array $propertyGroups
     * @param array $groups
     * @param $exclude
     */
    public function testUninitializedContextIsWorking(array $propertyGroups, array $groups, $exclude)
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'prop', 'propVal');
        $metadata->groups = $propertyGroups;

        $strat = new GroupsExclusionStrategy($groups);
        $this->assertEquals($strat->shouldSkipProperty($metadata, SerializationContext::create()), $exclude);
    }

    public function getExclusionRules()
    {
        return [
            [['foo'], ['foo'], false],
            [['foo'], [], true],
            [[], ['foo'], true],
            [['foo'], ['bar'], true],
            [['bar'], ['foo'], true],

            [['foo', 'Default'], [], false],
            [['foo', 'bar'], [], true],
            [['foo', 'bar'], ['Default'], true],
            [['foo', 'bar'], ['foo'], false],

            [['foo', 'Default'], ['test'], true],
            [['foo', 'Default', 'test'], ['test'], false],

            [['foo'], ['Default'], true],
            [['Default'], [], false],
            [[], ['Default'], false],
            [['Default'], ['Default'], false],
            [['Default', 'foo'], ['Default'], false],
            [['Default'], ['Default','foo'], false],
            [['foo'], ['Default','foo'], false],
        ];
    }
}
