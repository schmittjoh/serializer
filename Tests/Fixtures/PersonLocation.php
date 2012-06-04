<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\XmlRoot;
use JMS\SerializerBundle\Annotation\Type;

/**
 * @XmlRoot("person_location")
 */
class PersonLocation
{
    /**
     * @Type("JMS\SerializerBundle\Tests\Fixtures\Person")
     */
    public $person;

    /**
     * @Type("string")
     */
    public $location;
}
