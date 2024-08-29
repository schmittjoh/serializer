<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DoctrinePHPCR;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Document;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Field;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Id;
use Doctrine\ODM\PHPCR\Mapping\Attributes\ReferenceMany;
use Doctrine\ODM\PHPCR\Mapping\Attributes\ReferenceOne;
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
#[XmlRoot(name: 'blog-post')]
#[Document]
class BlogPost
{
    /**
     * @PHPCRODM\Id()
     */
    #[Id]
    protected $id;

    /**
     * @PHPCRODM\Field(type="string")
     * @Groups({"comments","post"})
     */
    #[Groups(groups: ['comments', 'post'])]
    #[Field(type: 'string')]
    private $title;

    /**
     * @PHPCRODM\Field(type="string")
     */
    #[Field(type: 'string')]
    protected $slug;

    /**
     * @PHPCRODM\Field(type="date")
     * @XmlAttribute
     */
    #[XmlAttribute]
    #[Field(type: 'date')]
    private $createdAt;

    /**
     * @PHPCRODM\Field(type="boolean")
     * @Type("integer")
     * This boolean to integer conversion is one of the few changes between this
     * and the standard BlogPost class. It's used to test the override behavior
     * of the DoctrineTypeDriver so notice it, but please don't change it.
     * @SerializedName("is_published")
     * @Groups({"post"})
     * @XmlAttribute
     */
    #[Field(type: 'boolean')]
    #[Type(name: 'integer')]
    #[SerializedName(name: 'is_published')]
    #[Groups(groups: ['post'])]
    #[XmlAttribute]
    private $published;

    /**
     * @PHPCRODM\ReferenceMany(targetDocument="Comment", property="blogPost")
     * @XmlList(inline=true, entry="comment")
     * @Groups({"comments"})
     */
    #[XmlList(entry: 'comment', inline: true)]
    #[Groups(groups: ['comments'])]
    #[ReferenceMany(targetDocument:'Comment', property:'blogPost')]
    private $comments;

    /**
     * @PHPCRODM\ReferenceOne(targetDocument="Author")
     * @Groups({"post"})
     */
    #[Groups(groups: ['post'])]
    #[ReferenceOne(targetDocument:'Author')]
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
