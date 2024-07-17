<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

use JMS\Serializer\Tests\Fixtures\DiscriminatedAuthor;
use JMS\Serializer\Tests\Fixtures\DiscriminatedComment;
use JMS\Serializer\Annotation\UnionDiscriminator;

class ComplexDiscriminatedUnion
{
    #[UnionDiscriminator(field: 'objectType', map: ['author' => 'JMS\Serializer\Tests\Fixtures\DiscriminatedAuthor', 'comment' => 'JMS\Serializer\Tests\Fixtures\DiscriminatedComment'])]
    private DiscriminatedAuthor|DiscriminatedComment $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData(): Author|Comment
    {
        return $this->data;
    }
}

