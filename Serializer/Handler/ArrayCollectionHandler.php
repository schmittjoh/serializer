<?php

namespace JMS\SerializerBundle\Serializer\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\SerializerBundle\Serializer\GraphNavigator;
use JMS\SerializerBundle\Serializer\VisitorInterface;
use Doctrine\Common\Collections\Collection;
use JMS\SerializerBundle\Serializer\Handler\SubscribingHandlerInterface;

class ArrayCollectionHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');
        $collectionTypes = array('ArrayCollection', 'Doctrine\Common\Collections\ArrayCollection', 'Doctrine\ORM\PersistentCollection', 'Doctrine\ODM\MongoDB\PersistentCollection');

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

    public function serializeCollection(VisitorInterface $visitor, Collection $collection, array $type)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';

        return $visitor->visitArray($collection->toArray(), $type);
    }

    public function deserializeCollection(VisitorInterface $visitor, $data, array $type)
    {
        // See above.
        $type['name'] = 'array';

        return new ArrayCollection($visitor->visitArray($data, $type));
    }
}
