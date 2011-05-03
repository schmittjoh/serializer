<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\Type;

class Comment
{
    /**
     * @Type("JMS\SerializerBundle\Tests\Fixtures\Author")
     */
    private $author;

    /**
     * @Type("string")
     */
    private $text;

    public function __construct(Author $author, $text)
    {
        $this->author = $author;
        $this->text = $text;
    }
}