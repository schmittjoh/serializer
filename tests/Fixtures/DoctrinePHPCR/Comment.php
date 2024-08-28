<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DoctrinePHPCR;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Document;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Field;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Id;
use Doctrine\ODM\PHPCR\Mapping\Attributes\ReferenceOne;

/** @PHPCRODM\Document */
#[Document]
class Comment
{
    /**
     * @PHPCRODM\Id()
     */
    #[Id]
    protected $id;

    /**
     * @PHPCRODM\ReferenceOne(targetDocument="Author")
     */
    #[ReferenceOne(targetDocument:'Author')]
    private $author;

    /** @PHPCRODM\ReferenceOne(targetDocument="BlogPost") */
    private $blogPost;

    /**
     * @PHPCRODM\Field(type="string")
     */
    #[Field(type: 'string')]
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
