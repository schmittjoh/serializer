<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class JsonSerializableHandler
{
    public function __invoke(SerializationVisitorInterface $visitor, \JsonSerializable $object)
    {
        return $object->jsonSerialize();
    }
}
