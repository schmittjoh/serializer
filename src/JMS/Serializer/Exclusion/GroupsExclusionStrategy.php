<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Context;

class GroupsExclusionStrategy implements ExclusionStrategyInterface
{
    const DEFAULT_GROUP = 'Default';

    private $groups = array();

    public function __construct(array $groups)
    {
        if (empty($groups)) {
            $groups = array(self::DEFAULT_GROUP);
        }

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
        /* 
         * Walk the metadata stack to determine the active groups.
         * TODO: There should be some sort of caching
         */
        $groups = $this->groups;
        if ($navigatorContext->getMetadataStack()) {
            $groupModifiers = array();
            foreach ($navigatorContext->getMetadataStack() as $metadata) {
                if ($metadata instanceof PropertyMetadata && is_array($metadata->recursionGroups)) {
                    $groupModifiers[] = $metadata->recursionGroups;
                }
            }
            foreach (array_reverse($groupModifiers) as $modifier) {
                if (isset($modifier['set'])) {
                    $groups = $modifier['set'];
                }
                if (isset($modifier['add'])) {
                    foreach ($modifier['add'] as $group) {
                        $groups[$group] = true;
                    }
                }
                if (isset($modifier['remove'])) {
                    foreach ($modifier['remove'] as $group) {
                        unset($groups[$group]);
                    }
                }
            }
        }

        if ( ! $property->groups) {
            return ! isset($groups[self::DEFAULT_GROUP]);
        }

        foreach ($property->groups as $group) {
            if (isset($groups[$group])) {
                return false;
            }
        }

        return true;
    }
}
