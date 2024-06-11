<?php

declare(strict_types=1);

namespace JMS\Serializer\EventDispatcher;

class PreSerializeEvent extends ObjectEvent
{
    public function setType(string $typeName, array $params = []): void
    {
        $this->type = ['name' => $typeName, 'params' => $params];
    }
}
