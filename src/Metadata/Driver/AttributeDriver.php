<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

class AttributeDriver extends AnnotationOrAttributeDriver
{
    /**
     * @return list<object>
     */
    protected function getClassAnnotations(\ReflectionClass $class): array
    {
        return array_map(
            static function (\ReflectionAttribute $attribute): object {
                return $attribute->newInstance();
            },
            $class->getAttributes()
        );
    }

    /**
     * @return list<object>
     */
    protected function getMethodAnnotations(\ReflectionMethod $method): array
    {
        return array_map(
            static function (\ReflectionAttribute $attribute): object {
                return $attribute->newInstance();
            },
            $method->getAttributes()
        );
    }

    /**
     * @return list<object>
     */
    protected function getPropertyAnnotations(\ReflectionProperty $property): array
    {
        return array_map(
            static function (\ReflectionAttribute $attribute): object {
                return $attribute->newInstance();
            },
            $property->getAttributes()
        );
    }
}
