<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\XmlAttribute;
use JMS\SerializerBundle\Annotation\XmlValue;
use JMS\SerializerBundle\Annotation\XmlRoot;
use JMS\SerializerBundle\Annotation\Type;

/**
 * @XmlRoot("child")
 */
class Person
{
    /**
     * @Type("string")
     * @XmlValue
     */
    public $name;

    /**
     * @Type("integer")
     * @XmlAttribute
     */
    public $age;
}
