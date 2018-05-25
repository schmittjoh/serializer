<?php

namespace JMS\Serializer\Tests\Fixtures\DoctrinePHPCR;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/** @PHPCRODM\Document */
class Comment
{
    /**
     * @PHPCRODM\Id()
     */
    protected $id;

    /**
     * @PHPCRODM\ReferenceOne(targetDocument="Author")
     */
    private $author;

    /** @PHPCRODM\ReferenceOne(targetDocument="BlogPost") */
    private $blogPost;

    /**
     * @PHPCRODM\Field(type="string")
     */
    private $text;

    public function __construct(Author $author, $text)
    {
        $this->author = $author;
        $this->text = $text;
        $this->blogPost = new ArrayCollection();
    }

    public function getAuthor()
    {
        return $this->author;
    }
}
