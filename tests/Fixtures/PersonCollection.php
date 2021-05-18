<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("person_collection")
 */
#[XmlRoot(name: 'person_collection')]
class PersonCollection
{
    /**
     * @Type("ArrayCollection<JMS\Serializer\Tests\Fixtures\Person>")
     * @XmlList(entry = "person", inline = true)
     */
    #[Type(name: 'ArrayCollection<JMS\Serializer\Tests\Fixtures\Person>')]
    #[XmlList(entry: 'person', inline: true)]
    public $persons;

    /**
     * @Type("string")
     */
    #[Type(name: 'string')]
    public $location;

    public function __construct()
    {
        $this->persons = new ArrayCollection();
    }
}
