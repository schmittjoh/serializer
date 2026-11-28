<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\NonVisitableTypeException;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Type\Type;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

/**
 * @phpstan-import-type TypeArray from Type
 */
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

    /**
     * @param TypeArray $type
     */
    public function serializeUnion(
        SerializationVisitorInterface $visitor,
        mixed $data,
        array $type,
        SerializationContext $context
    ): mixed {
        $dataType = gettype($data);
        if (!$this->isPrimitiveType($dataType)) {
            $dataType = get_class($data);
        }

        foreach ($type['params'][0] ?? [] as $possibleType) {
            if ($this->matchesType($possibleType['name'], $dataType)) {
                return $context->getNavigator()->accept($data, $possibleType);
            }
        }

        $resolvedType = [
            'name' => $dataType,
            'params' => [],
        ];

        return $context->getNavigator()->accept($data, $resolvedType);
    }

    /**
     * @param TypeArray $type
     */
    public function deserializeUnion(DeserializationVisitorInterface $visitor, mixed $data, array $type, DeserializationContext $context): mixed
    {
        if ($data instanceof \SimpleXMLElement) {
            throw new RuntimeException('XML deserialisation into union types is not supported yet.');
        }

        if (3 === count($type['params'])) {
            $lookupField = $type['params'][1];
            if (empty($data[$lookupField])) {
                throw new NonVisitableTypeException(sprintf('Union Discriminator Field "%s" not found in data', $lookupField));
            }

            $unionMap = $type['params'][2];
            $lookupValue = $data[$lookupField];
            if (empty($unionMap[$lookupValue])) {
                throw new NonVisitableTypeException(sprintf('Union Discriminator Map does not contain key "%s"', $lookupValue));
            }

            $finalType = [
                'name' => $unionMap[$lookupValue],
                'params' => [],
            ];

            return $context->getNavigator()->accept($data, $finalType);
        }

        $dataType = gettype($data);

        foreach ($type['params'][0] as $possibleType) {
            if ($this->matchesType($possibleType['name'], $dataType)) {
                return $context->getNavigator()->accept($data, $possibleType);
            }
        }

        foreach ($type['params'][0] as $possibleType) {
            if ($this->isPrimitiveType($possibleType['name']) && $this->testPrimitive($data, $possibleType['name'])) {
                return $context->getNavigator()->accept($data, $possibleType);
            }
        }

        throw new NotAcceptableException();
    }

    private function isPrimitiveType(string $type): bool
    {
        return in_array($type, ['int', 'integer', 'float', 'double', 'bool', 'boolean', 'true', 'false', 'string', 'array'], true);
    }

    private function matchesType(string $declaredType, string $dataType): bool
    {
        return $declaredType === $dataType
            || (isset(self::$aliases[$dataType]) && $declaredType === self::$aliases[$dataType]);
    }

    private function testPrimitive(mixed $data, string $type): bool
    {
        switch ($type) {
            case 'array':
                return is_array($data);

            case 'integer':
            case 'int':
                return (string) (int) $data === (string) $data;

            case 'double':
            case 'float':
                return (string) (float) $data === (string) $data;

            case 'bool':
            case 'boolean':
                return !is_array($data) && (string) (bool) $data === (string) $data;

            case 'true':
                return true === $data;

            case 'false':
                return false === $data;

            case 'string':
                return !is_array($data) && !is_object($data);
        }

        return false;
    }
}
