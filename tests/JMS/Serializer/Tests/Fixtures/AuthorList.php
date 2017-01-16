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

use JMS\Serializer\Annotation as Serializer;

/**
 * An array-acting object that holds many author instances.
 */
class AuthorList implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @Serializer\Type("array<JMS\Serializer\Tests\Fixtures\Author>")
     * @var array
     */
    protected $authors = array();

    /**
     * @param Author $author
     */
    public function add(Author $author)
    {
        $this->authors[] = $author;
    }

    /**
     * @see IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->authors);
    }

    /**
     * @see Countable
     */
    public function count()
    {
        return count($this->authors);
    }

    /**
     * @see ArrayAccess
     */
    public function offsetExists($offset)
    {
        return isset($this->authors[$offset]);
    }

    /**
     * @see ArrayAccess
     */
    public function offsetGet($offset)
    {
        return isset($this->authors[$offset]) ? $this->authors[$offset] : null;
    }

    /**
     * @see ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->authors[] = $value;
        } else {
            $this->authors[$offset] = $value;
        }
    }

    /**
     * @see ArrayAccess
     */
    public function offsetUnset($offset)
    {
        unset($this->authors[$offset]);
    }

}
