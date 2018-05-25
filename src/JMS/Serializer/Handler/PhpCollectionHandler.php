<?php

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use PhpCollection\Map;
use PhpCollection\Sequence;

class PhpCollectionHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');
        $collectionTypes = array(
            'PhpCollection\Sequence' => 'Sequence',
            'PhpCollection\Map' => 'Map',
        );

        foreach ($collectionTypes as $type => $shortName) {
            foreach ($formats as $format) {
                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serialize' . $shortName,
                );

                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'deserialize' . $shortName,
                );
            }
        }

        return $methods;
    }

    public function serializeMap(VisitorInterface $visitor, Map $map, array $type, Context $context)
    {
        $type['name'] = 'array';

        return $visitor->visitArray(iterator_to_array($map), $type, $context);
    }

    public function deserializeMap(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $type['name'] = 'array';

        return new Map($visitor->visitArray($data, $type, $context));
    }

    public function serializeSequence(VisitorInterface $visitor, Sequence $sequence, array $type, Context $context)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';

        return $visitor->visitArray($sequence->all(), $type, $context);
    }

    public function deserializeSequence(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        // See above.
        $type['name'] = 'array';

        return new Sequence($visitor->visitArray($data, $type, $context));
    }
}
