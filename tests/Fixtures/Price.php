<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;

/**
 * @XmlRoot("price")
 */
class Price
{
    /**
     * @Type("float")
     * @XmlValue
     */
    private $price;

    public function __construct($price)
    {
        $this->price = $price;
    }
}
