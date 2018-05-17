<?php

declare(strict_types=1);

/*
 * Copyright 2018 Rene Gerritsen <rene.gerritsen@me.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
    protected $ip;

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
     * @param string $ip
     * @param string $serverId
     */
    public function __construct($name, $ip, $serverId)
    {
        $this->name = $name;
        $this->ip = $ip;
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
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getServerId()
    {
        return $this->serverId;
    }
}
