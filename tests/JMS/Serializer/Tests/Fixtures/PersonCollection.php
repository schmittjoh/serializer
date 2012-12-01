<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlList;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;

/**
 * @XmlRoot("person_collection")
 */
class PersonCollection
{
    /**
     * @Type("ArrayCollection<JMS\Serializer\Tests\Fixtures\Person>")
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
