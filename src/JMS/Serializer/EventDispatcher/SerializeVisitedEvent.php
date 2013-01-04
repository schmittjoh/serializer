<?php

namespace JMS\Serializer\EventDispatcher;

class SerializeVisitedEvent extends Event
{
    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }
}
