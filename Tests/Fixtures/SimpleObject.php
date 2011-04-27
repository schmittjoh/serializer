<?php

namespace JMS\SerializerExtraBundle\Tests\Fixtures;

class SimpleObject
{
    private $foo;

    /**
     * @SerializedName("moo")
     */
    private $bar;

    protected $camelCase = 'boo';

    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
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