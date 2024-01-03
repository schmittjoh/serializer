<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver\AttributeDriver;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Annotation\SerializerAttribute;
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
        $attributes = $class->getAttributes(SerializerAttribute::class, \ReflectionAttribute::IS_INSTANCEOF);

        return array_merge($this->reader->getClassAnnotations($class), $this->buildAnnotations($attributes));
    }

    public function getClassAnnotation(ReflectionClass $class, $annotationName): ?object
    {
        $attributes = $class->getAttributes($annotationName, \ReflectionAttribute::IS_INSTANCEOF);

        return $this->reader->getClassAnnotation($class, $annotationName) ?? $this->buildAnnotation($attributes);
    }

    public function getMethodAnnotations(ReflectionMethod $method): array
    {
        $attributes = $method->getAttributes(SerializerAttribute::class, \ReflectionAttribute::IS_INSTANCEOF);

        return array_merge($this->reader->getMethodAnnotations($method), $this->buildAnnotations($attributes));
    }

    public function getMethodAnnotation(ReflectionMethod $method, $annotationName): ?object
    {
        $attributes = $method->getAttributes($annotationName, \ReflectionAttribute::IS_INSTANCEOF);

        return $this->reader->getClassAnnotation($method, $annotationName) ?? $this->buildAnnotation($attributes);
    }

    public function getPropertyAnnotations(ReflectionProperty $property): array
    {
        $attributes = $property->getAttributes(SerializerAttribute::class, \ReflectionAttribute::IS_INSTANCEOF);

        return array_merge($this->reader->getPropertyAnnotations($property), $this->buildAnnotations($attributes));
    }

    public function getPropertyAnnotation(ReflectionProperty $property, $annotationName): ?object
    {
        $attributes = $property->getAttributes($annotationName, \ReflectionAttribute::IS_INSTANCEOF);

        return $this->reader->getClassAnnotation($property, $annotationName) ?? $this->buildAnnotation($attributes);
    }

    /**
     * @param list<\ReflectionAttribute<SerializerAttribute>> $attributes
     */
    private function buildAnnotation(array $attributes): ?SerializerAttribute
    {
        if (!isset($attributes[0])) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    /**
     * @return list<SerializerAttribute>
     */
    private function buildAnnotations(array $attributes): array
    {
        return array_map(
            static fn (\ReflectionAttribute $attribute): object => $attribute->newInstance(),
            $attributes,
        );
    }
}
