<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use Metadata\ClassMetadata as BaseClassMetadata;
use Metadata\Driver\DriverInterface;

/**
 * This class decorates any other driver. If the inner driver does not provide a
 * a property type, the decorator will guess based on Doctrine 2 metadata.
 */
abstract class AbstractDoctrineTypeDriver implements DriverInterface
{
    /**
     * Map of doctrine 2 field types to JMS\Serializer types
     *
     * @var array
     */
    protected $fieldMapping = [
        'string' => 'string',
        'ascii_string' => 'string',
        'text' => 'string',
        'blob' => 'string',
        'guid' => 'string',
        'decimal' => 'string',

        'integer' => 'integer',
        'smallint' => 'integer',
        'bigint' => 'integer',

        'datetime' => 'DateTime',
        'datetimetz' => 'DateTime',
        'time' => 'DateTime',
        'date' => 'DateTime',

        'datetime_immutable' => 'DateTimeImmutable',
        'datetimetz_immutable' => 'DateTimeImmutable',
        'time_immutable' => 'DateTimeImmutable',
        'date_immutable' => 'DateTimeImmutable',

        'dateinterval' => 'DateInterval',

        'float' => 'float',

        'boolean' => 'boolean',

        'array' => 'array',
        'json_array' => 'array',
        'simple_array' => 'array<string>',
    ];
    /**
     * @var DriverInterface
     */
    protected $delegate;
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var ParserInterface
     */
    protected $typeParser;

    public function __construct(DriverInterface $delegate, ManagerRegistry $registry, ?ParserInterface $typeParser = null)
    {
        $this->delegate = $delegate;
        $this->registry = $registry;
        $this->typeParser = $typeParser ?: new Parser();
    }

    public function loadMetadataForClass(\ReflectionClass $class): ?BaseClassMetadata
    {
        $classMetadata = $this->delegate->loadMetadataForClass($class);

        if (null === $classMetadata) {
            return null;
        }

        \assert($classMetadata instanceof ClassMetadata);
        // Abort if the given class is not a mapped entity
        if (!$doctrineMetadata = $this->tryLoadingDoctrineMetadata($class->name)) {
            return $classMetadata;
        }

        $this->setDiscriminator($doctrineMetadata, $classMetadata);

        // We base our scan on the internal driver's property list so that we
        // respect any internal allow/blocklist like in the AnnotationDriver
        foreach ($classMetadata->propertyMetadata as $key => $propertyMetadata) {
            // If the inner driver provides a type, don't guess anymore.
            if ($propertyMetadata->type || $this->isVirtualProperty($propertyMetadata)) {
                continue;
            }

            if ($this->hideProperty($doctrineMetadata, $propertyMetadata)) {
                unset($classMetadata->propertyMetadata[$key]);
            }

            $this->setPropertyType($doctrineMetadata, $propertyMetadata);
        }

        return $classMetadata;
    }

    private function isVirtualProperty(PropertyMetadata $propertyMetadata): bool
    {
        return $propertyMetadata instanceof VirtualPropertyMetadata
            || $propertyMetadata instanceof StaticPropertyMetadata
            || $propertyMetadata instanceof ExpressionPropertyMetadata;
    }

    protected function setDiscriminator(DoctrineClassMetadata $doctrineMetadata, ClassMetadata $classMetadata): void
    {
    }

    protected function hideProperty(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata): bool
    {
        return false;
    }

    protected function setPropertyType(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata): void
    {
    }

    protected function tryLoadingDoctrineMetadata(string $className): ?DoctrineClassMetadata
    {
        if (!$manager = $this->registry->getManagerForClass($className)) {
            return null;
        }

        if ($manager->getMetadataFactory()->isTransient($className)) {
            return null;
        }

        return $manager->getClassMetadata($className);
    }

    protected function normalizeFieldType(string $type): ?string
    {
        if (!isset($this->fieldMapping[$type])) {
            return null;
        }

        return $this->fieldMapping[$type];
    }
}
