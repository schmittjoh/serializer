<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @ORM\Entity
 *
 * @XmlRoot("blog-post")
 */
#[XmlRoot(name: 'blog-post')]
class BlogPost extends AbstractBlogPost
{
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="blogPost")
     *
     * @XmlList(inline=true, entry="comment")
     * @Groups({"comments"})
     */
    #[XmlList(entry: 'comment', inline: true)]
    #[Groups(groups: ['comments'])]
    private $comments;

    public function __construct($title, Author $author, \DateTime $createdAt)
    {
        parent::__construct($title, $author, $createdAt);
        $this->comments = new ArrayCollection();
    }

    public function addComment(Comment $comment)
    {
        $this->comments->add($comment);
    }
}
