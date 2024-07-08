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
    private static $aliases = ['boolean' => 'bool', 'integer' => 'int', 'double' => 'float'];

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['json', 'xml'];

        foreach ($formats as $format) {
            $methods[] = [
                'type' => 'union',
                'format' => $format,
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'method' => 'deserializeUnion',
            ];
            $methods[] = [
                'type' => 'union',
                'format' => $format,
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serializeUnion',
            ];
        }

        return $methods;
    }

    public function serializeUnion(
        SerializationVisitorInterface $visitor,
        mixed $data,
        array $type,
        SerializationContext $context
    ) {
        return $this->matchSimpleType($data, $type, $context);
    }

    public function deserializeUnion(DeserializationVisitorInterface $visitor, mixed $data, array $type, DeserializationContext $context)
    {
        if ($data instanceof \SimpleXMLElement) {
            throw new RuntimeException('XML deserialisation into union types is not supported yet.');
        }

        return $this->matchSimpleType($data, $type, $context);
    }

    private function matchSimpleType(mixed $data, array $type, Context $context)
    {
        $dataType = $this->determineType($data, $type, $context->getFormat());
        $alternativeName = null;

        if (isset(static::$aliases[$dataType])) {
            $alternativeName = static::$aliases[$dataType];
        }

        foreach ($type['params'] as $possibleType) {
            if ($possibleType['name'] === $dataType || $possibleType['name'] === $alternativeName) {
                return $context->getNavigator()->accept($data, $possibleType);
            }
        }
    }

    private function determineType(mixed $data, array $type, string $format): ?string
    {
        foreach ($type['params'] as $possibleType) {
            if ($this->testPrimitive($data, $possibleType['name'], $format)) {
                return $possibleType['name'];
            }
        }

        return null;
    }

    private function testPrimitive(mixed $data, string $type, string $format): bool
    {
        switch ($type) {
            case 'integer':
            case 'int':
                return (string) (int) $data === (string) $data;

            case 'double':
            case 'float':
                return (string) (float) $data === (string) $data;

            case 'bool':
            case 'boolean':
                return (string) (bool) $data === (string) $data;

            case 'string':
                return (string) $data === (string) $data;
        }

        return false;
    }
}
