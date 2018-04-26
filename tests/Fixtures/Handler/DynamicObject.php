<?php


namespace JMS\Serializer\Tests\Fixtures\Handler;

use JMS\Serializer\Annotation as JMS;

class DynamicObject
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $string;

    public function setProperties()
    {
        $this->string = 'hello';
    }
}