<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\IdentityFields;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/** @ORM\Entity */
#[ORM\Entity]
class Server
{
    /**
     * @Serializer\Type("string")
     * @ORM\Id
     * @ORM\Column(type="string", name="ip_address")
     *
     * @var string
     */
    #[Serializer\Type(name: 'string')]
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, name: 'ip_address')]
    protected $ipAddress;

    /**
     * @Serializer\SerializedName("server_id_extracted")
     * @Serializer\Type("string")
     * @ORM\Id
     * @ORM\Column(type="string", name="server_id")
     *
     * @var string
     */
    #[Serializer\SerializedName(name: 'server_id_extracted')]
    #[Serializer\Type(name: 'string')]
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, name: 'server_id')]
    protected $serverId;

    /**
     * @Serializer\Type("string")
     * @ORM\Column(type="string")
     *
     * @var string
     */
    #[Serializer\Type(name: 'string')]
    #[ORM\Column(type: Types::STRING)]
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
