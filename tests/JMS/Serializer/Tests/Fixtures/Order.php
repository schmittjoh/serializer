<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\Type;

/** @XmlRoot("order") */
class Order
{
    /** @Type("JMS\Serializer\Tests\Fixtures\Price") */
    private $cost;

    public function __construct(Price $price = null)
    {
        $this->cost = $price ?: new Price(5);
    }
}