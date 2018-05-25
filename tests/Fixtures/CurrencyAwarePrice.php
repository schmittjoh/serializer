<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/** @Serializer\XmlRoot("price") */
class CurrencyAwarePrice
{
    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     */
    private $currency;

    /**
     * @Serializer\XmlValue
     * @Serializer\Type("double")
     */
    private $amount;

    public function __construct($amount, $currency = 'EUR')
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }
}
