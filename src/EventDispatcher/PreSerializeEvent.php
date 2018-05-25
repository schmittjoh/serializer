<?php

declare(strict_types=1);

namespace JMS\Serializer\EventDispatcher;

class PreSerializeEvent extends ObjectEvent
{
    /**
     * @param string $typeName
     * @param array $params
     */
    public function setType(string $typeName, array $params = [])
    {
        $this->type = ['name' => $typeName, 'params' => $params];
    }
}
