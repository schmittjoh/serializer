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

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\AdvancedNamingStrategyInterface;

/**
 * Class ContextualNamingStrategy
 *
 * Only use this class for testing purpose
 */
class ContextualNamingStrategy implements AdvancedNamingStrategyInterface
{
    public function getPropertyName(PropertyMetadata $property, Context $context)
    {
        if ($context->getDirection() == GraphNavigator::DIRECTION_SERIALIZATION) {
            return strtoupper($property->name);
        }
        return ucfirst($property->name);
    }
}
