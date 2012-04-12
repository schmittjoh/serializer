<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer\Exclusion;

use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Exception\RuntimeException;

class GroupsExclusionStrategy implements ExclusionStrategyInterface
{
    private $groups = array();

    public function __construct(array $groups)
    {
        if (empty($groups)) {
            throw new RuntimeException('Empty group array may not be configured for GroupsExclusionStrategy');
        }

        foreach ($groups as $group) {
            $this->groups[$group] = true;
        }
    }

    public function shouldSkipClass(ClassMetadata $metadata)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property)
    {
        if (!$property->groups) {
            return true;
        }

        foreach ($property->groups as $group) {
            if (isset($this->groups[$group])) {
                return false;
            }
        }

        return true;
    }
}
