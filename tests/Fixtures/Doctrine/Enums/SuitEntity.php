<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Enums;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SuitEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", enumType="JMS\Serializer\Tests\Fixtures\Enum\BackedSuitInt")
     */
    public $id;

    /**
     * @ORM\Column(type="string", enumType="JMS\Serializer\Tests\Fixtures\Enum\BackedSuit")
     */
    public $name;
}
