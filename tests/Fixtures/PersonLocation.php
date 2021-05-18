<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("person_location")
 */
#[XmlRoot(name: 'person_location')]
class PersonLocation
{
    /**
     * @Type("JMS\Serializer\Tests\Fixtures\Person")
     */
    #[Type(name: 'JMS\Serializer\Tests\Fixtures\Person')]
    public $person;

    /**
     * @Type("string")
     */
    #[Type(name: 'string')]
    public $location;
}
