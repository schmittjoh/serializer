<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\PersistentCollection as MongoPersistentCollection;
use Doctrine\ODM\PHPCR\PersistentCollection as PhpcrPersistentCollection;
use Doctrine\ORM\PersistentCollection as OrmPersistentCollection;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Metadata\PropertyMetadata;
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

    /**
     * @var ManagerRegistry|null
     */
    private $managerRegistry;

    public function __construct(
        bool $initializeExcluded = true,
        ?ManagerRegistry $managerRegistry = null
    ) {
        $this->initializeExcluded = $initializeExcluded;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['json', 'xml'];

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

        if (null === $this->managerRegistry) {
            return $elements;
        }

        $propertyMetadata = $context->getMetadataStack()->top();
        if (!$propertyMetadata instanceof PropertyMetadata) {
            return $elements;
        }

        $objectManager = $this->managerRegistry->getManagerForClass($propertyMetadata->class);
        if (null === $objectManager) {
            return $elements;
        }

        $classMetadata = $objectManager->getClassMetadata($propertyMetadata->class);
        $currentObject = $visitor->getCurrentObject();

        if (
            array_key_exists('name', $propertyMetadata->type)
            && in_array($propertyMetadata->type['name'], self::COLLECTION_TYPES)
            && $classMetadata->isCollectionValuedAssociation($propertyMetadata->name)
        ) {
            $existingCollection = $classMetadata->getFieldValue($currentObject, $propertyMetadata->name);
            if (!$existingCollection instanceof OrmPersistentCollection) {
                return $elements;
            }

            foreach ($elements as $element) {
                if (!$existingCollection->contains($element)) {
                    $existingCollection->add($element);
                }
            }

            foreach ($existingCollection as $collectionElement) {
                if (!$elements->contains($collectionElement)) {
                    $existingCollection->removeElement($collectionElement);
                }
            }

            return $existingCollection;
        }

        return $elements;
    }
}
