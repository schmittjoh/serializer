<?php

namespace JMS\Serializer\EventDispatcher;

class PreSerializeEvent extends Event
{
    /**
     * @param string $typeName
     * @param array $params
     */
    public function setType($typeName, array $params = array())
    {
        $this->type = array('name' => $typeName, 'params' => $params);
    }
}
