<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\DeserializationContext;
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
                'type' => \ArrayIterator::class,
                'format' => $format,
                'method' => 'serializeIterator',
            ];

            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'type' => \ArrayIterator::class,
                'format' => $format,
                'method' => 'deserializeIterator',
            ];
        }

        foreach (self::SUPPORTED_FORMATS as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => \Generator::class,
                'format' => $format,
                'method' => 'serializeIterator',
            ];

            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'type' => \Generator::class,
                'format' => $format,
                'method' => 'deserializeGenerator',
            ];
        }

        return $methods;
    }

    /**
     * @return mixed[]|\ArrayObject
     */
    public function serializeIterator(
        SerializationVisitorInterface $visitor,
        \Iterator $iterator,
        array $type,
        SerializationContext $context
    ) {
        $type['name'] = 'array';

        $context->stopVisiting($iterator);
        $result = $visitor->visitArray(iterator_to_array($iterator), $type);
        $context->startVisiting($iterator);

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

        return new \ArrayIterator($visitor->visitArray($data, $type));
    }


    /**
     * @param mixed $data
     */
    public function deserializeGenerator(
        DeserializationVisitorInterface $visitor,
        $data,
        array $type,
        DeserializationContext $context
    ): \Generator {
        return (static function () use (&$visitor, &$data, &$type): \Generator {
            $type['name'] = 'array';
            foreach ($visitor->visitArray($data, $type) as $key => $item) {
                yield $key => $item;
            }
        })();
    }
}
