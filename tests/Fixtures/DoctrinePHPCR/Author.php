<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DoctrinePHPCR;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Document;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Field;
use Doctrine\ODM\PHPCR\Mapping\Attributes\Id;
use JMS\Serializer\Annotation\SerializedName;

/** @PHPCRODM\Document */
#[Document]
class Author
{
    /**
     * @PHPCRODM\Id()
     */
    #[Id]
    protected $id;

    /**
     * @PHPCRODM\Field(type="string")
     * @SerializedName("full_name")
     */
    #[SerializedName(name: 'full_name')]
    #[Field(type: 'string')]
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
