<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\XmlRoot;
use JMS\SerializerBundle\Annotation\XmlList;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\SerializerBundle\Annotation\Type;

/**
 * @XmlRoot("person_collection")
 */
class PersonCollection
{
    /**
     * @Type("ArrayCollection<JMS\SerializerBundle\Tests\Fixtures\Person>")
     * @XmlList(entry = "person", inline = true)
     */
    public $persons;

    /**
     * @Type("string")
     */
    public $location;

    public function __construct()
    {
        $this->persons = new ArrayCollection;
    }
}
