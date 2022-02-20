<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @ORM\Entity
 *
 * @XmlRoot("blog-post")
 */
#[XmlRoot(name: 'blog-post')]
class BlogPost
{
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="guid")
     */
    private $guid;

    /**
     * @ORM\Column(type="string")
     *
     * @Groups({"comments","post"})
     */
    #[Groups(groups: ['comments', 'post'])]
    private $title;

    /**
     * @ORM\Column(type="some_custom_type")
     */
    protected $slug;

    /**
     * @ORM\Column(type="datetime")
     *
     * @XmlAttribute
     */
    #[XmlAttribute]
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Type("integer")
     * This boolean to integer conversion is one of the few changes between this
     * and the standard BlogPost class. It's used to test the override behavior
     * of the DoctrineTypeDriver so notice it, but please don't change it.
     * @SerializedName("is_published")
     * @Groups({"post"})
     * @XmlAttribute
     */
    #[Type(name: 'integer')]
    #[SerializedName(name: 'is_published')]
    #[Groups(groups: ['post'])]
    #[XmlAttribute]
    private $published;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="blogPost")
     *
     * @XmlList(inline=true, entry="comment")
     * @Groups({"comments"})
     */
    #[XmlList(entry: 'comment', inline: true)]
    #[Groups(groups: ['comments'])]
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="Author")
     *
     * @Groups({"post"})
     */
    #[Groups(groups: ['post'])]
    private $author;

    /**
     * @Serializer\Exclude()
     * @ORM\Column(type="integer")
     */
    #[Serializer\Exclude]
    private $ref;

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

    /**
     * @Serializer\VirtualProperty()
     */
    #[Serializer\VirtualProperty]
    public function getRef()
    {
        return $this->ref;
    }
}
