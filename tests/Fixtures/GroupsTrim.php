<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class GroupsTrim
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
