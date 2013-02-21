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

namespace JMS\Serializer\Naming;

use JMS\Serializer\Exception\RuntimeException;

/**
 * Description of ObjectTypeMapNamingStrategy
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ClassTypeMapNamingStrategy implements ClassTypeNamingStrategyInterface
{
    private $map = array();

    public function __construct(array $map = array())
    {
        $this->map = $map;
    }

    /**
     * {@inheritDoc}
     */
    public function classToType($class)
    {
        if (isset($this->map[$class])) {
            return $this->map[$class];
        }

        throw new RuntimeException(sprintf('Type is not mapped for class "%s"', $class));
    }

    /**
     * {@inheritDoc}
     */
    public function typeToClass($type)
    {
        if ($class = array_search($type, $this->map)) {
            return $class;
        }

        throw new RuntimeException(sprintf('Class is not mapped for type "%s"', $type));
    }
}
