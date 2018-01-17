<?php

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class ObjectHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = [];

        foreach (array('json', 'xml', 'yml') as $format) {
            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => $format,
                'type' => 'Object',
                'method' => 'serializeObject',
            ];
            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => $format,
                'type' => 'Object',
                'method' => 'deserializeObject',
            ];
        }

        return $methods;
    }

    public function serializeObject(JsonSerializationVisitor $visitor, $object, array $type, Context $context)
    {
        if ($object === null) {
            return null;
        }

        $class = get_class($object);
        $type['name'] = $class;
        $serialisation = $context->getNavigator()->accept($object, $type, $context);
        $serialisation['::class'] = $class;

        return $serialisation;
    }

    public function deserializeObject(JsonDeserializationVisitor $visitor, $data, array $type, Context $context)
    {
        if ($data === null) {
            return null;
        }

        if (!isset($data['::class'])) {
            throw new RuntimeException('The ::class property was missing for a dynamic object type.');
        }
        $type['name'] = $data['::class'];
        unset($data['::class']);
        return $context->getNavigator()->accept($data, $type, $context);
    }
}