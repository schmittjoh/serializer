<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\HandlerCallback;
use JMS\Serializer\Annotation\Type;

class ObjectWithHandlerCallbacks
{
    /**
     * @Type("string")
     */
    public $name;

    /**
     * @HandlerCallback(direction="serialization", format="json")
     */
    public function toJson()
    {
        return $this->name;
    }

    /**
     * @HandlerCallback(direction="serialization", format="xml")
     */
    public function toXml()
    {
        return $this->name;
    }
}
