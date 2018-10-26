<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\IdentityFields;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/** @ORM\Entity */
class Server
{
    /**
     * @Serializer\Type("string")
     * @ORM\Id
     * @ORM\Column(type="string", name="ip_address")
     *
     * @var string
     */
    protected $ipAddress;

    /**
     * @Serializer\SerializedName("server_id_extracted")
     * @Serializer\Type("string")
     * @ORM\Id
     * @ORM\Column(type="string", name="server_id")
     *
     * @var string
     */
    protected $serverId;

    /**
     * @Serializer\Type("string")
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

    /**
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
