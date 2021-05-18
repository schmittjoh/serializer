<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/** @Serializer\XmlRoot("price") */
#[Serializer\XmlRoot(name: 'price')]
class CurrencyAwarePrice
{
    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     */
    #[Serializer\XmlAttribute]
    #[Serializer\Type(name: 'string')]
    private $currency;

    /**
     * @Serializer\XmlValue
     * @Serializer\Type("double")
     */
    #[Serializer\XmlValue]
    #[Serializer\Type(name: 'double')]
    private $amount;

    public function __construct($amount, $currency = 'EUR')
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }
}
