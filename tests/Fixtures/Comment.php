<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class Comment
{
    /**
     * @Type("JMS\Serializer\Tests\Fixtures\Author")
     */
    private $author;

    /**
     * @Type("string")
     */
    private $text;

    public function __construct(?Author $author = null, $text)
    {
        $this->author = $author;
        $this->text = $text;
    }

    public function getAuthor()
    {
        return $this->author;
    }
}
