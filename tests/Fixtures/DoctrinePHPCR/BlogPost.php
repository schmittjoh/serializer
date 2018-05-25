<?php

namespace JMS\Serializer\Tests\Fixtures\DoctrinePHPCR;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @PHPCRODM\Document
 * @XmlRoot("blog-post")
 */
class BlogPost
{
    /**
     * @PHPCRODM\Id()
     */
    protected $id;

    /**
     * @PHPCRODM\Field(type="string")
     * @Groups({"comments","post"})
     */
    private $title;

    /**
     * @PHPCRODM\Field(type="string")
     */
    protected $slug;

    /**
     * @PHPCRODM\Field(type="date")
     * @XmlAttribute
     */
    private $createdAt;

    /**
     * @PHPCRODM\Field(type="boolean")
     * @Type("integer")
     * This boolean to integer conversion is one of the few changes between this
     * and the standard BlogPost class. It's used to test the override behavior
     * of the DoctrineTypeDriver so notice it, but please don't change it.
     *
     * @SerializedName("is_published")
     * @Groups({"post"})
     * @XmlAttribute
     */
    private $published;

    /**
     * @PHPCRODM\ReferenceMany(targetDocument="Comment", property="blogPost")
     * @XmlList(inline=true, entry="comment")
     * @Groups({"comments"})
     */
    private $comments;

    /**
     * @PHPCRODM\ReferenceOne(targetDocument="Author")
     * @Groups({"post"})
     */
    private $author;

    public function __construct($title, Author $author, \DateTime $createdAt)
    {
        $this->title = $title;
        $this->author = $author;
        $this->published = false;
        $this->comments = new ArrayCollection();
        $this->createdAt = $createdAt;
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
