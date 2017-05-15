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

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

class GroupsExclusionStrategy implements ExclusionStrategyInterface
{
    const DEFAULT_GROUP = 'Default';

    private $groups = array();

    public function __construct(array $groups)
    {
        if (empty($groups)) {
            $groups = array(self::DEFAULT_GROUP);
        }

        $this->groups = $groups;
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
        $groups = $this->getGroupsFor($navigatorContext);

        if (!$property->groups) {
            return !in_array(self::DEFAULT_GROUP, $groups);
        }

        return $this->shouldSkipUsingGroups($property, $groups);
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

    private function getGroupsFor(Context $navigatorContext)
    {
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
        }

        return $groups;
    }
}
