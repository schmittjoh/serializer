<?php

namespace JMS\Serializer\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;

class ArrayCollectionHandler implements SubscribingHandlerInterface
{
    /**
     * @var bool
     */
    private $initializeExcluded = true;

    public function __construct($initializeExcluded = true)
    {
        $this->initializeExcluded = $initializeExcluded;
    }

    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');
        $collectionTypes = array(
            'ArrayCollection',
            'Doctrine\Common\Collections\ArrayCollection',
            'Doctrine\ORM\PersistentCollection',
            'Doctrine\ODM\MongoDB\PersistentCollection',
            'Doctrine\ODM\PHPCR\PersistentCollection',
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

    public function serializeCollection(VisitorInterface $visitor, Collection $collection, array $type, Context $context)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';

        if ($this->initializeExcluded === false) {
            $exclusionStrategy = $context->getExclusionStrategy();
            if ($exclusionStrategy !== null && $exclusionStrategy->shouldSkipClass($context->getMetadataFactory()->getMetadataForClass(\get_class($collection)), $context)) {
                return $visitor->visitArray([], $type, $context);
            }
        }

        return $visitor->visitArray($collection->toArray(), $type, $context);
    }

    public function deserializeCollection(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        // See above.
        $type['name'] = 'array';

        return new ArrayCollection($visitor->visitArray($data, $type, $context));
    }
}
