<?php

declare(strict_types=1);

namespace JMS\Serializer\Construction;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManagerAware;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

/**
 * Object Manager Aware Constructor.
 * Injects Object manager on Construction in case ObjectManagerAware Interface is implemented
 */
class ObjectManagerAwareConstructor implements ObjectConstructorInterface
{
    /**
     * @var ObjectConstructorInterface
     */
    private $objectConstructor;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(
        ObjectConstructorInterface $objectConstructor,
        ManagerRegistry $managerRegistry
    ) {
        $this->objectConstructor = $objectConstructor;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function construct(
        DeserializationVisitorInterface $visitor,
        ClassMetadata $metadata,
        $data,
        array $type,
        DeserializationContext $context
    ): ?object {
        $object = $this->objectConstructor->construct(
            $visitor,
            $metadata,
            $data,
            $type,
            $context
        );
        if ($object instanceof ObjectManagerAware
            && $objectManager = $this->managerRegistry->getManagerForClass($metadata->name)
        ) {
            $doctrineMetadata = $objectManager->getClassMetadata($metadata->name);
            $object->injectObjectManager($objectManager, $doctrineMetadata);
        }
        return $object;
    }
}
