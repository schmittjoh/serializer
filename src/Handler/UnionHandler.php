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

    /**
     *  ReflectionUnionType::getTypes() returns the types sorted according to these rules:
     * - Classes, interfaces, traits, iterable (replaced by Traversable), ReflectionIntersectionType objects, parent and self:
     *     these types will be returned first, in the order in which they were declared.
     * - static and all built-in types (iterable replaced by array) will come next. They will always be returned in this order:
     *     static, callable, array, string, int, float, bool (or false or true), null.
     *
     * For determining types of primitives, it is necessary to reorder primitives so that they are tested from lowest specificity to highest:
     * i.e. null, true, false, int, float, bool, string
     */
    private function reorderTypes(array $type): array
    {
        if ($type['params']) {
            uasort($type['params'], static function ($a, $b) {
                $order = ['null' => 0, 'true' => 1, 'false' => 2, 'bool' => 3, 'int' => 4, 'float' => 5, 'string' => 6];

                return (array_key_exists($a['name'], $order) ? $order[$a['name']] : 7) <=> (array_key_exists($b['name'], $order) ? $order[$b['name']] : 7);
            });
        }

        return $type;
    }

    private function determineType(mixed $data, array $type, string $format): string|null
    {
        foreach ($this->reorderTypes($type)['params'] as $possibleType) {
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
