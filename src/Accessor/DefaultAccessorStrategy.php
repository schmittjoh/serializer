<?php

declare(strict_types=1);

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

namespace JMS\Serializer\Accessor;

use GeneratedHydrator\Configuration;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class DefaultAccessorStrategy implements AccessorStrategyInterface
{
    private $gen = array();

    public function getValue(object $object, PropertyMetadata $metadata, $context)
    {

        if (!$metadata->getter && array_key_exists($metadata->name, $context)) {
            return $context[$metadata->name];
        }

        return $metadata->getValue($object);
    }

    public function setValue(object $object, $value, PropertyMetadata $metadata): void
    {
        $metadata->setValue($object, $value);
    }

    public function startAccessing(object $object, ClassMetadata $metadata)
    {
        $class = get_class($object);

        if (!isset($this->gen[$class])) {
            $config = new Configuration($class);
            $hydratorClass = $config->createFactory()->getHydratorClass();
            $this->gen[$class] = new $hydratorClass();
        }

        return $this->gen[$class]->extract($object);
    }

    public function endAccessing(object $object, ClassMetadata $metadata, $context)
    {

    }
}
