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
     * @return array|\ArrayObject|null
     */
    public function serializeIterable(
        SerializationVisitorInterface $visitor,
        iterable $iterable,
        array $type,
        SerializationContext $context,
        GraphNavigatorInterface $navigator
    ): ?iterable {
        $type['name'] = 'array';

        $context->stopVisiting($iterable);
        $result = $visitor->visitArray(Functions::iterableToArray($iterable), $type, $navigator);
        $context->startVisiting($iterable);

        return $result;
    }

    /**
     * @param mixed $data
     */
    public static  function deserializeIterator(
        DeserializationVisitorInterface $visitor,
        $data,
        array $type,
        DeserializationContext $context,
        GraphNavigatorInterface $navigator
    ): \Iterator {
        $type['name'] = 'array';

        return new ArrayIterator($visitor->visitArray($data, $type, $navigator));
    }

    /**
     * @param mixed $data
     */
    public static function deserializeGenerator(
        DeserializationVisitorInterface $visitor,
        $data,
        array $type
    ): Generator {
        return (static function ($visitor, $data, $type): Generator {
            $type['name'] = 'array';
            yield from $visitor->visitArray($data, $type);
        })($visitor, $data, $type);
    }
}
