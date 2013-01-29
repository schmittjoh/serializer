<?php

namespace JMS\Serializer\Handler;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use PhpCollection\Sequence;

class PhpCollectionHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');
        $collectionTypes = array(
            'PhpCollection\Sequence' => 'Sequence',
        );

        foreach ($collectionTypes as $type => $shortName) {
            foreach ($formats as $format) {
                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serialize'.$shortName,
                );

                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'deserialize'.$shortName,
                );
            }
        }

        return $methods;
    }

    public function serializeSequence(VisitorInterface $visitor, Sequence $sequence, array $type)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';

        return $visitor->visitArray($sequence->all(), $type);
    }

    public function deserializeSequence(VisitorInterface $visitor, $data, array $type)
    {
        // See above.
        $type['name'] = 'array';

        return new Sequence($visitor->visitArray($data, $type));
    }
}