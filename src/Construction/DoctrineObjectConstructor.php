<?php

declare(strict_types=1);

namespace JMS\Serializer\Construction;

use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\ObjectConstructionException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

/**
 * Doctrine object constructor for new (or existing) objects during deserialization.
 */
final class DoctrineObjectConstructor implements ObjectConstructorInterface
{
    public const ON_MISSING_NULL = 'null';
    public const ON_MISSING_EXCEPTION = 'exception';
    public const ON_MISSING_FALLBACK = 'fallback';
    /**
     * @var string
     */
    private $fallbackStrategy;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var ObjectConstructorInterface
     */
    private $fallbackConstructor;

    /**
     * @param ManagerRegistry $managerRegistry     Manager registry
     * @param ObjectConstructorInterface $fallbackConstructor Fallback object constructor
     */
    public function __construct(ManagerRegistry $managerRegistry, ObjectConstructorInterface $fallbackConstructor, string $fallbackStrategy = self::ON_MISSING_NULL)
    {
        $this->managerRegistry = $managerRegistry;
        $this->fallbackConstructor = $fallbackConstructor;
        $this->fallbackStrategy = $fallbackStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function construct(DeserializationVisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context): ?object
    {
        // Locate possible ObjectManager
        $objectManager = $this->managerRegistry->getManagerForClass($metadata->name);

        if (!$objectManager) {
            // No ObjectManager found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Locate possible ClassMetadata
        $classMetadataFactory = $objectManager->getMetadataFactory();

        if ($classMetadataFactory->isTransient($metadata->name)) {
            // No ClassMetadata found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Managed entity, check for proxy load
        if (!\is_array($data)) {
            // Single identifier, load proxy
            return $objectManager->getReference($metadata->name, $data);
        }

        // Fallback to default constructor if missing identifier(s)
        $classMetadata = $objectManager->getClassMetadata($metadata->name);
        $identifierList = [];

        foreach ($classMetadata->getIdentifierFieldNames() as $name) {
            if (isset($metadata->propertyMetadata[$name])) {
                $dataName = $metadata->propertyMetadata[$name]->serializedName;
            } else {
                $dataName = $name;
            }

            if (!array_key_exists($dataName, $data)) {
                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }
            $identifierList[$name] = $data[$dataName];
        }

        if (empty($identifierList)) {
            // $classMetadataFactory->isTransient() fails on embeddable class with file metadata driver
            // https://github.com/doctrine/persistence/issues/37
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Entity update, load it from database
        $object = $objectManager->find($metadata->name, $identifierList);

        if (null === $object) {
            switch ($this->fallbackStrategy) {
                case self::ON_MISSING_NULL:
                    return null;
                case self::ON_MISSING_EXCEPTION:
                    throw new ObjectConstructionException(sprintf('Entity %s can not be found', $metadata->name));
                case self::ON_MISSING_FALLBACK:
                    return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
                default:
                    throw new InvalidArgumentException('The provided fallback strategy for the object constructor is not valid');
            }
        }

        $objectManager->initializeObject($object);

        return $object;
    }
}
