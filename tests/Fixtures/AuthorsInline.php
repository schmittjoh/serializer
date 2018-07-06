<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class AuthorsInline
{
    /**
     * @Serializer\Type("array<JMS\Serializer\Tests\Fixtures\Author>")
     * @Serializer\Inline()
     */
    private $collection;

    public function __construct(Author ... $authors)
    {
        $this->collection = $authors;
    }
}
