<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithXmlListWithObjectTypeB implements ObjectWithXmlListWithObjectTypesInterface
{
    /**
     * @var string
     * @Serializer\Type(name="string")
     */
    private $bar;

    /**
     * @param string $bar
     */
    public function __construct($bar = null)
    {
        $this->bar = $bar;
    }
}
