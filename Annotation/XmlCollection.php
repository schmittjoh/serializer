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

abstract class XmlCollection
{
    public $inline = false;
    public $entry = 'entry';

    public function __construct(array $values)
    {
        if (isset($values['entry'])) {
            if (!is_string($values['entry'])) {
                throw new InvalidArgumentException(sprintf('Value for attribute "entry" must be a string, but got %s.', json_encode($values['entry'])));
            }
            $this->entry = $values['entry'];
        }

        if (isset($values['inline'])) {
            if (!is_bool($values['inline'])) {
                throw new InvalidArgumentException(sprintf('Value for attribute "inline" must be a boolean, but got %s.', json_encode($values['inline'])));
            }
            $this->inline = $values['inline'];
        }
    }
}