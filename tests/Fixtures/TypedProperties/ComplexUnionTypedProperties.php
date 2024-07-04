<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\Comment;

class ComplexUnionTypedProperties
{
    private Author|Comment $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData(): Author|Comment
    {
        return $this->data;
    }
}

