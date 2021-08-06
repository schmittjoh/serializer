<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver\AttributeDriver;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

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

    public function getClassAnnotations(ReflectionClass $class)
    {
        $attributes = $class->getAttributes();

        return array_merge($this->reader->getClassAnnotations($class), $this->buildAnnotations($attributes));
    }

    public function getClassAnnotation(ReflectionClass $class, $annotationName)
    {
        $attributes = $class->getAttributes($annotationName);

        return $this->reader->getClassAnnotation($class, $annotationName) ?? $this->buildAnnotation($attributes);
    }

    public function getMethodAnnotations(ReflectionMethod $method)
    {
        $attributes = $method->getAttributes();

        return array_merge($this->reader->getMethodAnnotations($method), $this->buildAnnotations($attributes));
    }

    public function getMethodAnnotation(ReflectionMethod $method, $annotationName)
    {
        $attributes = $method->getAttributes($annotationName);

        return $this->reader->getClassAnnotation($method, $annotationName) ?? $this->buildAnnotation($attributes);
    }

    public function getPropertyAnnotations(ReflectionProperty $property)
    {
        $attributes = $property->getAttributes();

        return array_merge($this->reader->getPropertyAnnotations($property), $this->buildAnnotations($attributes));
    }

    public function getPropertyAnnotation(ReflectionProperty $property, $annotationName)
    {
        $attributes = $property->getAttributes($annotationName);

        return $this->reader->getClassAnnotation($property, $annotationName) ?? $this->buildAnnotation($attributes);
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
