<?php

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use PropelCollection;

class PropelCollectionHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');
        //Note: issue when handling inheritance
        $collectionTypes = array(
            'PropelCollection',
            'PropelObjectCollection',
            'PropelArrayCollection',
            'PropelOnDemandCollection'
        );

        foreach ($collectionTypes as $type) {
            foreach ($formats as $format) {
                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serializeCollection',
                );

                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'deserializeCollection',
                );
            }
        }

        return $methods;
    }

    public function serializeCollection(VisitorInterface $visitor, PropelCollection $collection, array $type, Context $context)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';

        return $visitor->visitArray($collection->getData(), $type, $context);
    }

    public function deserializeCollection(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        // See above. Set parameter type to PropelCollection<T> or PropelCollection<K,V>
        $type['name'] = 'array';

        $collection = new PropelCollection();
        $collection->setData($visitor->visitArray($data, $type, $context));

        return $collection;
    }
}
