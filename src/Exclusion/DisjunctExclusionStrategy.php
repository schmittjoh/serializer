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

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use PhpCollection\Sequence;
use PhpCollection\SequenceInterface;

/**
 * Disjunct Exclusion Strategy.
 *
 * This strategy is short-circuiting and will skip a class, or property as soon as one of the delegates skips it.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class DisjunctExclusionStrategy implements ExclusionStrategyInterface
{
    /** @var \PhpCollection\SequenceInterface */
    private $delegates;

    /**
     * @param ExclusionStrategyInterface[]|SequenceInterface $delegates
     */
    public function __construct($delegates)
    {
        if ( ! $delegates instanceof SequenceInterface) {
            $delegates = new Sequence($delegates);
        }

        $this->delegates = $delegates;
    }

    public function addStrategy(ExclusionStrategyInterface $strategy)
    {
        $this->delegates->add($strategy);
    }

    /**
     * Whether the class should be skipped.
     *
     * @param ClassMetadata $metadata
     *
     * @return boolean
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $context)
    {
        foreach ($this->delegates as $delegate) {
            /** @var $delegate ExclusionStrategyInterface */
            if ($delegate->shouldSkipClass($metadata, $context)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the property should be skipped.
     *
     * @param PropertyMetadata $property
     *
     * @return boolean
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $context)
    {
        foreach ($this->delegates as $delegate) {
            /** @var $delegate ExclusionStrategyInterface */
            if ($delegate->shouldSkipProperty($property, $context)) {
                return true;
            }
        }

        return false;
    }
}
