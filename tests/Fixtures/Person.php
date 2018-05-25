<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;

/**
 * @XmlRoot("child")
 */
class Person
{
    /**
     * @Type("string")
     * @XmlValue(cdata=false)
     */
    public $name;

    /**
     * @Type("int")
     * @XmlAttribute
     */
    public $age;
}
