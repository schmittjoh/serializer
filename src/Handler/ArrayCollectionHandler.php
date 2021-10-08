<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\PersistentCollection as MongoPersistentCollection;
use Doctrine\ODM\PHPCR\PersistentCollection as PhpcrPersistentCollection;
use Doctrine\ORM\PersistentCollection as OrmPersistentCollection;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class ArrayCollectionHandler implements SubscribingHandlerInterface
{
    public const COLLECTION_TYPES = [
        'ArrayCollection',
        ArrayCollection::class,
        OrmPersistentCollection::class,
        MongoPersistentCollection::class,
        PhpcrPersistentCollection::class,
    ];

    /**
     * @var bool
     */
    private $initializeExcluded;

    public function __construct(bool $initializeExcluded = true)
    {
        $this->initializeExcluded = $initializeExcluded;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['json', 'xml', 'yml'];

        foreach (self::COLLECTION_TYPES as $type) {
            foreach ($formats as $format) {
                $methods[] = [
                    'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serializeCollection',
                ];

                $methods[] = [
                    'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'deserializeCollection',
                ];
            }
        }

        return $methods;
    }

    /**
     * @return array|\ArrayObject
     */
    public function serializeCollection(SerializationVisitorInterface $visitor, Collection $collection, array $type, SerializationContext $context)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';

        $context->stopVisiting($collection);

        if (false === $this->initializeExcluded) {
            $exclusionStrategy = $context->getExclusionStrategy();
            if (null !== $exclusionStrategy && $exclusionStrategy->shouldSkipClass($context->getMetadataFactory()->getMetadataForClass(\get_class($collection)), $context)) {
                $context->startVisiting($collection);

                return $visitor->visitArray([], $type);
            }
        }

        $result = $visitor->visitArray($collection->toArray(), $type);

        $context->startVisiting($collection);

        return $result;
    }

    /**
     * @param mixed $data
     */
    public function deserializeCollection(
        DeserializationVisitorInterface $visitor,
        $data,
        array $type,
        DeserializationContext $context
    ): Collection {
        // See above.
        $type['name'] = 'array';

        $elements = new ArrayCollection($visitor->visitArray($data, $type));

        if ($collection = $context->removePersistentCollectionForCurrentPath()) {
            foreach ($elements as $element) {
                if (!$collection->contains($element)) {
                    $collection->add($element);
                }
            }

            foreach ($collection as $collectionElement) {
                if (!$elements->contains($collectionElement)) {
                    $collection->removeElement($collectionElement);
                }
            }

            return $collection;
        } else {
            return $elements;
        }
    }
}
