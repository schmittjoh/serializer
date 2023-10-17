<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;

/** @ORM\Entity */
#[ORM\Entity]
class AuthorExcludedId
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @Exclude
     */
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[Exclude]
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @SerializedName("full_name")
     */
    #[ORM\Column(type: Types::STRING)]
    #[SerializedName(name: 'full_name')]
    private $name;

    public function __construct($name, $id = null)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }
}
