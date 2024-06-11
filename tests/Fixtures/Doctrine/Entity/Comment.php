<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
#[ORM\Entity]
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Author")
     */
    #[ORM\OneToOne(targetEntity: Author::class)]
    private $author;

    /** @ORM\ManyToOne(targetEntity="BlogPost") */
    #[ORM\ManyToOne(targetEntity: BlogPost::class)]
    private $blogPost;

    /**
     * @ORM\Column(type="string")
     */
    #[ORM\Column(type: Types::STRING)]
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
