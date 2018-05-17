<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithXmlListWithObjectTypeA implements ObjectWithXmlListWithObjectTypesInterface
{
    /**
     * @var string
     * @Serializer\Type(name="string")
     */
    private $foo;

    /**
     * @param string $foo
     */
    public function __construct($foo = null)
    {
        $this->foo = $foo;
    }
}
