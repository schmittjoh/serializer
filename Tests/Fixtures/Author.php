<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\SerializedName;
use JMS\SerializerBundle\Annotation\Type;

class Author
{
    /**
     * @Type("string")
     * @SerializedName("full_name")
     */
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}