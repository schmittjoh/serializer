<?php

/*
 * Copyright 2014 Adam Pullen <adam@finalconcept.com.au>
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
 *
 * This annotation is used to exclude properties from object based on their path.
 *
 * Given the following object graph
 * User
 *   > groups
 *     > permissions
 *
 * You can exclude permissions by using the PathBasedExclusion annotation on your
 * class like so
 *
 * @PathBasedExclusion(["groups.permissions"])
 * class User {
 *  ...
 * }
 * 
 * This annotation always works from the Root node. PathBasedExclusion annotations
 * will be ignored on child classes. If you have a child class that also needs
 * to exclude some properties you will need to add them to your root class
 */

namespace JMS\Serializer\Annotation;

use JMS\Serializer\Exception\RuntimeException;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class PathBasedExclusion
{

    public $paths;

    public function __construct(array $values)
    {

        if (!is_array($values['value'])) {
            throw new RuntimeException('"value" must be an array of object paths.');
        }
        $this->paths = $values['value'];
    }
}
