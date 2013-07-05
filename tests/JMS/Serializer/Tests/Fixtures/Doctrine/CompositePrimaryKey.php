<?php
namespace JMS\Serializer\Tests\Fixtures\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

class CompositePrimaryKey
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @Serializer\SerializedName("id_first_serialized")
     */
    protected $idFirst;

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @Serializer\SerializedName("id_second_serialized")
     */
    private $idSecond;

    public function getIdFirst()
    {
        return $this->idFirst;
    }

    public function setIdFirst($idFirst)
    {
        $this->idFirst = $idFirst;
    }

    public function getIdSecond()
    {
        return $this->idSecond;
    }

    public function setIdSecond($idSecond)
    {
        $this->idSecond = $idSecond;
    }

}
