<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;

/** @XmlRoot("order") */
class CurrencyAwareOrder
{
    /** @Type("JMS\Serializer\Tests\Fixtures\CurrencyAwarePrice") */
    private $cost;

    public function __construct(CurrencyAwarePrice $price = null)
    {
        $this->cost = $price ?: new CurrencyAwarePrice(5);
    }
}
