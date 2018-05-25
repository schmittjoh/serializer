<?php

namespace JMS\Serializer\Tests\Fixtures\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;

/** @ORM\Entity */
class Author
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @SerializedName("full_name")
     */
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
