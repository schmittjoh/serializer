<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

use JMS\Serializer\Annotation\UnionDiscriminator;
use JMS\Serializer\Tests\Fixtures\DiscriminatedAuthor;
use JMS\Serializer\Tests\Fixtures\DiscriminatedComment;

class ComplexDiscriminatedUnion
{
    #[UnionDiscriminator(field: 'type')]
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
