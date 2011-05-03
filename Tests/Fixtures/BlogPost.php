<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\Type;
use Doctrine\Common\Collections\ArrayCollection;

class BlogPost
{
    /**
     * @Type("string")
     */
    private $title;

    /**
     * @Type("DateTime")
     */
    private $createdAt;

    /**
     * @Type("boolean")
     */
    private $published;

    /**
     * @Type("ArrayCollection<JMS\SerializerBundle\Tests\Fixtures\Comment>")
     */
    private $comments;

    /**
     * @Type("JMS\SerializerBundle\Tests\Fixtures\Author")
     */
    private $author;

    public function __construct($title, Author $author)
    {
        $this->title = $title;
        $this->author = $author;
        $this->published = false;
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function setPublished()
    {
        $this->published = true;
    }

    public function addComment(Comment $comment)
    {
        $this->comments->add($comment);
    }
}