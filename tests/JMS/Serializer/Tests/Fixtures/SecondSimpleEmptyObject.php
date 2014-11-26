<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class SecondSimpleEmptyObject
{
    /**
     * @SerializedName("second_moo")
     * @Type("string")
     */
    private $bar;

    public function __construct($bar)
    {
        $this->bar = $bar;
    }
}
