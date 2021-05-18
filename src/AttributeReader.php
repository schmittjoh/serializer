<?php

declare(strict_types=1);

namespace JMS\Serializer;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AttributeReader implements Reader
{
    public function getClassAnnotations(ReflectionClass $class)
    {
        $result = [];
        $attributes = $class->getAttributes();
        foreach ($attributes as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
    }

    public function getClassAnnotation(ReflectionClass $class, $annotationName)
    {
        $attributes = $class->getAttributes($annotationName);
        if (0 === count($attributes) || null === $attributes[0]) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    public function getMethodAnnotations(ReflectionMethod $method)
    {
        $result = [];
        $attributes = $method->getAttributes();
        foreach ($attributes as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
    }

    public function getMethodAnnotation(ReflectionMethod $method, $annotationName)
    {
        $attributes = $method->getAttributes($annotationName);
        if (0 === count($attributes) || null === $attributes[0]) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    public function getPropertyAnnotations(ReflectionProperty $property)
    {
        $result = [];
        $attributes = $property->getAttributes();
        foreach ($attributes as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
    }

    public function getPropertyAnnotation(ReflectionProperty $property, $annotationName)
    {
        $attributes = $property->getAttributes($annotationName);
        if (0 === count($attributes) || null === $attributes[0]) {
            return null;
        }

        return $attributes[0]->newInstance();
    }
}
