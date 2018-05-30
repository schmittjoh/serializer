<?php

namespace JMS\Serializer\Tests\Fixtures\Doctrine\IdentityFields;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/** @ORM\Entity */
class Server
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="ip_address")
     * @Serializer\Type("string")
     * @var string
     */
    protected $ipAddress;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="server_id")
     * @Serializer\SerializedName("server_id_extracted")
     * @Serializer\Type("string")
     * @var string
     */
    protected $serverId;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Type("string")
     * @var string
     */
    private $name;

    /**
     * Server constructor.
     * @param string $name
     * @param string $ipAddress
     * @param string $serverId
     */
    public function __construct($name, $ipAddress, $serverId)
    {
        $this->name = $name;
        $this->ipAddress = $ipAddress;
        $this->serverId = $serverId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @return string
     */
    public function getServerId()
    {
        return $this->serverId;
    }
}
