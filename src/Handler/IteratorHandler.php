<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use ArrayIterator;
use Generator;
use Iterator;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Functions;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class IteratorHandler implements SubscribingHandlerInterface
{
    private const SUPPORTED_FORMATS = ['json', 'xml'];

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];

        foreach (self::SUPPORTED_FORMATS as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => iterable::class,
                'format' => $format,
                'method' => 'serializeIterable',
            ];

            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'type' => iterable::class,
                'format' => $format,
                'method' => 'deserializeIterable',
            ];
        }

        foreach (self::SUPPORTED_FORMATS as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => Iterator::class,
                'format' => $format,
                'method' => 'serializeIterable',
            ];

            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'type' => Iterator::class,
                'format' => $format,
                'method' => 'deserializeIterator',
            ];
        }

        foreach (self::SUPPORTED_FORMATS as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => ArrayIterator::class,
                'format' => $format,
                'method' => 'serializeIterable',
            ];

            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'type' => ArrayIterator::class,
                'format' => $format,
                'method' => 'deserializeIterator',
            ];
        }

        foreach (self::SUPPORTED_FORMATS as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => Generator::class,
                'format' => $format,
                'method' => 'serializeIterable',
            ];

            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'type' => Generator::class,
                'format' => $format,
                'method' => 'deserializeGenerator',
            ];
        }

        return $methods;
    }

    /**
     * @return mixed
     */
    public function serializeIterable(
        SerializationVisitorInterface $visitor,
        iterable $iterable,
        array $type,
        SerializationContext $context
    ) {
        $type['name'] = 'array';

        $context->stopVisiting($iterable);
        $result = $visitor->visitArray(Functions::iterableToArray($iterable), $type);
        $context->startVisiting($iterable);

        return $result;
    }

    /**
     * @param mixed $data
     */
    public function deserializeIterator(
        DeserializationVisitorInterface $visitor,
        $data,
        array $type,
        DeserializationContext $context
    ): \Iterator {
        $type['name'] = 'array';

        return new ArrayIterator($visitor->visitArray($data, $type));
    }

    /**
     * @param mixed $data
     */
    public function deserializeIterable(
        DeserializationVisitorInterface $visitor,
        $data,
        array $type,
        DeserializationContext $context
    ): array {
        $type['name'] = 'array';

        $return = [];
        foreach ($visitor->visitArray($data, $type) as $key => $item) {
            $return[$key] = $item;
        }

        return $return;
    }


    /**
     * @param mixed $data
     */
    public function deserializeGenerator(
        DeserializationVisitorInterface $visitor,
        $data,
        array $type,
        DeserializationContext $context
    ): Generator {
        return (static function () use (&$visitor, &$data, &$type): Generator {
            $type['name'] = 'array';
            yield from $visitor->visitArray($data, $type);
        })();
    }
}
