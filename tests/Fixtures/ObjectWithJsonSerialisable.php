<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

final class ObjectWithJsonSerialisable
{
    /**
     * @Serializer\Type("JsonSerializable")
     */
    #[Serializer\Type(name: \JsonSerializable::class)]
    public $author;

    public function __construct(Author $author)
    {
        $this->author = $author;
    }
}
