<?php

namespace JMS\Serializer\Tests\Fixtures;

class ObjectWithToString
{
    private $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function __toString()
    {
        return $this->input;
    }
}
