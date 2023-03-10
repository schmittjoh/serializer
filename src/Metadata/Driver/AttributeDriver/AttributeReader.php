<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver\AttributeDriver;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * @deprecated use {@see \JMS\Serializer\Metadata\Driver\AttributeDriver} instead
 */
class AttributeReader implements Reader
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getClassAnnotations(ReflectionClass $class): array
    {
        $attributes = $class->getAttributes();

        return array_merge($this->reader->getClassAnnotations($class), $this->buildAnnotations($attributes));
    }

    public function getClassAnnotation(ReflectionClass $class, $annotationName): ?object
    {
        $attributes = $class->getAttributes($annotationName);

        return $this->reader->getClassAnnotation($class, $annotationName) ?? $this->buildAnnotation($attributes);
    }

    public function getMethodAnnotations(ReflectionMethod $method): array
    {
        $attributes = $method->getAttributes();

        return array_merge($this->reader->getMethodAnnotations($method), $this->buildAnnotations($attributes));
    }

    public function getMethodAnnotation(ReflectionMethod $method, $annotationName): ?object
    {
        $attributes = $method->getAttributes($annotationName);

        return $this->reader->getMethodAnnotation($method, $annotationName) ?? $this->buildAnnotation($attributes);
    }

    public function getPropertyAnnotations(ReflectionProperty $property): array
    {
        $attributes = $property->getAttributes();

        return array_merge($this->reader->getPropertyAnnotations($property), $this->buildAnnotations($attributes));
    }

    public function getPropertyAnnotation(ReflectionProperty $property, $annotationName): ?object
    {
        $attributes = $property->getAttributes($annotationName);

        return $this->reader->getPropertyAnnotation($property, $annotationName) ?? $this->buildAnnotation($attributes);
    }

    private function buildAnnotation(array $attributes): ?object
    {
        if (!isset($attributes[0])) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    private function buildAnnotations(array $attributes): array
    {
        $result = [];
        foreach ($attributes as $attribute) {
            if (0 === strpos($attribute->getName(), 'JMS\Serializer\Annotation\\')) {
                $result[] = $attribute->newInstance();
            }
        }

        return $result;
    }
}
