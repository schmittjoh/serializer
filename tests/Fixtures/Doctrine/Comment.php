<?php

namespace JMS\Serializer\Tests\Fixtures\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Comment
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="Author")
     */
    private $author;

    /** @ORM\ManyToOne(targetEntity="BlogPost") */
    private $blogPost;

    /**
     * @ORM\Column(type="string")
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
