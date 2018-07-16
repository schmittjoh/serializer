<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

class MultilineGroupsFormat
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    public function __construct($amount, $currency)
    {
        $this->amount = (int) $amount;
        $this->currency = $currency;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
}
