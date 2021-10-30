<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * An array-acting object that holds many author instances.
 */
class AuthorList implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @Serializer\Type("array<JMS\Serializer\Tests\Fixtures\Author>")
     *
     * @var array
     */
    #[Serializer\Type(name: 'array<JMS\Serializer\Tests\Fixtures\Author>')]
    protected $authors = [];

    public function add(Author $author)
    {
        $this->authors[] = $author;
    }

    /**
     * @see IteratorAggregate
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->authors);
    }

    /**
     * @see Countable
     */
    public function count(): int
    {
        return count($this->authors);
    }

    /**
     * @see ArrayAccess
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->authors[$offset]);
    }

    /**
     * @see ArrayAccess
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->authors[$offset] ?? null;
    }

    /**
     * @see ArrayAccess
     */
    #[\ReturnTypeWillChange]
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
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->authors[$offset]);
    }
}
