<?php

namespace JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance;

use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="orgType", type="string")
 * @ORM\DiscriminatorMap({
 *     "school" = "JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\School"
 * })
 */
abstract class Organization
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     * @ORM\Column(type = "integer")
     */
    private $id;
}


