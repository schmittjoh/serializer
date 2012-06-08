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

namespace JMS\SerializerBundle\Annotation;

use JMS\SerializerBundle\Exception\InvalidArgumentException;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD"})
 */
final class XmlMap extends XmlCollection
{
    public $keyAttribute = '_key';

    public function __construct(array $values)
    {
        parent::__construct($values);

        if (isset($values['keyAttribute'])) {
            if (!is_string($values['keyAttribute'])) {
                throw new InvalidArgumentException(sprintf('The attribute "keyAttribute" of @XmlMap must be a string, but got %s.', json_encode($values['keyAttribute'])));
            }
            $this->keyAttribute = $values['keyAttribute'];
        }
    }
}