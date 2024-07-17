<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\Comment;
use JMS\Serializer\Tests\Fixtures\MoreSpecificAuthor;

class ComplexUnionTypedProperties
{
    private Author|Comment|MoreSpecificAuthor $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData(): Author|Comment|MoreSpecificAuthor
    {
        return $this->data;
    }
}
