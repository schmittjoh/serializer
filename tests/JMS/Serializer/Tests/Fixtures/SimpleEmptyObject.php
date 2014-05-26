<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class SimpleEmptyObject
{
    /** @Type("stdClass") */
    private $foo;

    /**
     * @SerializedName("moo")
     * @Type("string")
     */
    private $bar;

    public function __construct($bar)
    {
        $this->foo = new \stdClass();
        $this->bar = $bar;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
