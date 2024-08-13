<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

use JMS\Serializer\Annotation\UnionDiscriminator;
use JMS\Serializer\Tests\Fixtures\DiscriminatedAuthor;
use JMS\Serializer\Tests\Fixtures\DiscriminatedComment;

class ComplexDiscriminatedUnion
{
    #[UnionDiscriminator(field: 'objectType', map: ['author' => 'JMS\Serializer\Tests\Fixtures\DiscriminatedAuthor', 'comment' => 'JMS\Serializer\Tests\Fixtures\DiscriminatedComment'])]
    private DiscriminatedAuthor|DiscriminatedComment $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData(): DiscriminatedAuthor|DiscriminatedComment
    {
        return $this->data;
    }
}
