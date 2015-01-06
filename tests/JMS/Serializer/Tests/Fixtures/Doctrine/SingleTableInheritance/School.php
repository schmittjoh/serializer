<?php

namespace JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance;

use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class School extends Organization
{
}

