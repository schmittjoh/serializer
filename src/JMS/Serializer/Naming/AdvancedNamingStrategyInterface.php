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

namespace JMS\Serializer\Naming;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Interface for advanced property naming strategies.
 *
 * Implementations translate the property name to a serialized name that is
 * displayed. It allows advanced strategy thanks to context parameter.
 *
 * @author Vincent Rasquier <vincent.rsbs@gmail.com>
 */
interface AdvancedNamingStrategyInterface
{
    /**
     * Translates the name of the property to the serialized version.
     *
     * @param PropertyMetadata $property
     * @param Context $context
     *
     * @return string
     */
    public function getPropertyName(PropertyMetadata $property, Context $context);
}
