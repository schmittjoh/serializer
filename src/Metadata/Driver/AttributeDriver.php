<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use Metadata\ClassMetadata as BaseClassMetadata;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableInterface;

class AttributeDriver implements DriverInterface
{
    private $annotationDriver;

    private $decorated;

    public function __construct(AnnotationDriver $annotationDriver, DriverInterface $decorated)
    {
        $this->annotationDriver = $annotationDriver;
        $this->decorated = $decorated;
    }

    public function loadMetadataForClass(\ReflectionClass $class): ?BaseClassMetadata
    {
        $metadata = $this->decorated->loadMetadataForClass($class);
        $attributeMetadata = !$class->isInternal() ? $this->annotationDriver->loadMetadataForClass($class) : null;
        if ($metadata instanceof MergeableInterface && $attributeMetadata instanceof MergeableInterface) {
            $metadata->merge($attributeMetadata);
        }

        return $metadata ?: $attributeMetadata;
    }
}
