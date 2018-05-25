<?php

namespace JMS\Serializer\EventDispatcher;

class PreSerializeEvent extends ObjectEvent
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
