<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlValue;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\Type;

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
