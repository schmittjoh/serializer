<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Xml;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Tests\Fixtures\Doctrine\IdentityFields\Server;

class Result
{
    /**
     * @Serializer\XmlList(inline = true, entry="server")
     * @Serializer\Type("ArrayCollection<JMS\Serializer\Tests\Fixtures\Doctrine\IdentityFields\Server>")
     * @var Server[]|ArrayCollection
     */
    private $servers;

    /**
     *  /**
     * @return Server[]|ArrayCollection
     */
    public function getServers()
    {
        return $this->servers;
    }
}
