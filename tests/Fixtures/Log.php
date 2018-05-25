<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlMap;
use JMS\Serializer\Annotation\XmlRoot;

/** @XmlRoot("log") */
class Log
{
    /**
     * @SerializedName("author_list")
     * @XmlMap
     * @Type("AuthorList")
     */
    private $authors;

    /**
     * @XmlList(inline=true, entry = "comment")
     * @Type("array<JMS\Serializer\Tests\Fixtures\Comment>")
     */
    private $comments;

    public function __construct()
    {
        $this->authors = new AuthorList();
        $this->authors->add(new Author('Johannes Schmitt'));
        $this->authors->add(new Author('John Doe'));

        $author = new Author('Foo Bar');
        $this->comments = array();
        $this->comments[] = new Comment($author, 'foo');
        $this->comments[] = new Comment($author, 'bar');
        $this->comments[] = new Comment($author, 'baz');
    }
}
