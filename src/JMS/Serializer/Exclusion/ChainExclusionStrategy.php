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

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\NavigatorContext;

/**
 * Applies multiple exclusion strategies in
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ChainExclusionStrategy implements ExclusionStrategyInterface
{
    private $chain = array();

    public function __construct(array $chain = array())
    {
        foreach ($chain as $strategy) {
            $this->addExclusionStrategy($strategy);
        }
    }

    public function addExclusionStrategy(ExclusionStrategyInterface $strategy)
    {
        $this->chain[get_class($strategy)] = $strategy;
    }

    public function removeExclusionStrategy($strategy)
    {
        if (is_string($strategy)) {
            unset($this->chain[$strategy]);
        } elseif (false !== ($index = array_search($strategy, $this->chain, true))) {
            unset($this->chain[$index]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, NavigatorContext $navigatorContext)
    {
        foreach ($this->chain as $strategy) {
            if ($strategy->shouldSkipClass($metadata, $navigatorContext)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, NavigatorContext $navigatorContext)
    {
        foreach ($this->chain as $strategy) {
            if ($strategy->shouldSkipProperty($property, $navigatorContext)) {
                return true;
            }
        }

        return false;
    }
}
