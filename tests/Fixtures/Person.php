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
#[XmlRoot(name: 'child')]
class Person
{
    public const ALTERNATE_SERIALIZED_NAME = 'personName';

    /**
     * @Type("string")
     * @XmlValue(cdata=false)
     */
    #[Type(name: 'string')]
    #[XmlValue(cdata: false)]
    public $name;

    /**
     * @Type("int")
     * @XmlAttribute
     */
    #[Type(name: 'int')]
    #[XmlAttribute]
    public $age;
}
