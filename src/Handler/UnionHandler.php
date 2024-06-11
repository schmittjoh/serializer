<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class UnionHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'type' => 'union',
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'method' => 'deserializeUnion',
            ],
            [
                'type' => 'union',
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'xml',
                'method' => 'deserializeUnion',
            ],
            [
                'type' => 'union',
                'format' => 'json',
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serializeUnion',
            ],
            [
                'type' => 'union',
                'format' => 'xml',
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serializeUnion',
            ],
        ];
    }

    public function serializeUnion(
        SerializationVisitorInterface $visitor,
        $data,
        array $type,
        SerializationContext $context
    ) {
        return $this->matchSimpleType($data, $type, $context);
    }

    /**
     * @param int|string|\SimpleXMLElement $data
     * @param array $type
     */
    public function deserializeUnion(DeserializationVisitorInterface $visitor, $data, array $type, DeserializationContext $context)
    {
        if ($data instanceof \SimpleXMLElement) {
            throw new RuntimeException('XML deserialisation into union types is not supported yet.');
        }

        return $this->matchSimpleType($data, $type, $context);
    }

    private function matchSimpleType($data, array $type, Context $context)
    {
        $dataType = gettype($data);
        $alternativeName = null;
        switch ($dataType) {
            case 'boolean':
                $alternativeName = 'bool';
                break;
            case 'integer':
                $alternativeName = 'int';
                break;
            case 'double':
                $alternativeName = 'float';
                break;
            case 'array':
            case 'string':
                break;
            default:
                throw new RuntimeException();
        }

        foreach ($type['params'] as $possibleType) {
            if ($possibleType['name'] === $dataType || $possibleType['name'] === $alternativeName) {
                return $context->getNavigator()->accept($data, $possibleType);
            }
        }
    }
}
