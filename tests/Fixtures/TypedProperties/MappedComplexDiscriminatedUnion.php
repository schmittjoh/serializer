<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

use JMS\Serializer\Tests\Fixtures\MappedDiscriminatedAuthor;
use JMS\Serializer\Tests\Fixtures\MappedDiscriminatedComment;
use JMS\Serializer\Annotation\UnionDiscriminator;

class MappedComplexDiscriminatedUnion
{
    #[UnionDiscriminator(field: 'objectType', map: ['author' => 'JMS\Serializer\Tests\Fixtures\MappedDiscriminatedAuthor', 'comment' => 'JMS\Serializer\Tests\Fixtures\MappedDiscriminatedComment'])]
    private MappedDiscriminatedAuthor|MappedDiscriminatedComment $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData(): Author|Comment
    {
        return $this->data;
    }
}

