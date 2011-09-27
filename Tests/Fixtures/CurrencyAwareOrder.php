<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\XmlRoot;
use JMS\SerializerBundle\Annotation\Type;

/** @XmlRoot("order") */
class CurrencyAwareOrder
{
    /** @Type("JMS\SerializerBundle\Tests\Fixtures\CurrencyAwarePrice") */
    private $cost;

    public function __construct(CurrencyAwarePrice $price = null)
    {
        $this->cost = $price ?: new CurrencyAwarePrice(5);
    }
}