<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use function array_filter;

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
            array_filter(
                $class->getAttributes(),
                static function (\ReflectionAttribute $attribute): bool {
                    return class_exists($attribute->getName());
                }
            )
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
            array_filter(
                $method->getAttributes(),
                static function (\ReflectionAttribute $attribute): bool {
                    return class_exists($attribute->getName());
                }
            )
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
            array_filter(
                $property->getAttributes(),
                static function (\ReflectionAttribute $attribute): bool {
                    return class_exists($attribute->getName());
                }
            )
        );
    }
}
