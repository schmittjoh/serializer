<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use JMS\Serializer\Metadata\ClassMetadata as SerializerClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;

class TypedPropertiesDriver implements DriverInterface
{
    /**
     * @var DriverInterface
     */
    protected $delegate;

    /**
     * @var ParserInterface
     */
    protected $typeParser;

    /**
     * @var string[]
     */
    private $allowList;

    /**
     * @param string[] $allowList
     */
    public function __construct(DriverInterface $delegate, ?ParserInterface $typeParser = null, array $allowList = [])
    {
        $this->delegate = $delegate;
        $this->typeParser = $typeParser ?: new Parser();
        $this->allowList = array_merge($allowList, $this->getDefaultWhiteList());
    }

    /**
     *  ReflectionUnionType::getTypes() returns the types sorted according to these rules:
     * - Classes, interfaces, traits, iterable (replaced by Traversable), ReflectionIntersectionType objects, parent and self:
     *     these types will be returned first, in the order in which they were declared.
     * - static and all built-in types (iterable replaced by array) will come next. They will always be returned in this order:
     *     static, callable, array, string, int, float, bool (or false or true), null.
     *
     * For determining types of primitives, it is necessary to reorder primitives so that they are tested from lowest specificity to highest:
     * i.e. null, true, false, int, float, bool, string
     */
    private function reorderTypes(array $types): array
    {
        uasort($types, static function ($a, $b) {
            $order = ['null' => 0, 'true' => 1, 'false' => 2, 'bool' => 3, 'int' => 4, 'float' => 5, 'string' => 6];

            return ($order[$a['name']] ?? 7) <=> ($order[$b['name']] ?? 7);
        });

        return $types;
    }

    private function getDefaultWhiteList(): array
    {
        return [
            'int',
            'float',
            'bool',
            'boolean',
            'string',
            'double',
            'iterable',
            'resource',
        ];
    }

    /**
     * @return SerializerClassMetadata|null
     */
    public function loadMetadataForClass(ReflectionClass $class): ?ClassMetadata
    {
        $classMetadata = $this->delegate->loadMetadataForClass($class);

        if (null === $classMetadata) {
            return null;
        }

        \assert($classMetadata instanceof SerializerClassMetadata);

        // We base our scan on the internal driver's property list so that we
        // respect any internal allow/blocklist like in the AnnotationDriver
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            // If the inner driver provides a type, don't guess anymore.
            if ($propertyMetadata->type) {
                continue;
            }

            try {
                $reflectionType = $this->getReflectionType($propertyMetadata);

                if ($this->shouldTypeHint($reflectionType)) {
                    $type = $reflectionType->getName();

                    $propertyMetadata->setType($this->typeParser->parse($type));
                } elseif ($this->shouldTypeHintUnion($reflectionType)) {
                    $propertyMetadata->setType([
                        'name' => 'union',
                        'params' => [
                            $this->reorderTypes(
                                array_map(
                                    fn (string $type) => $this->typeParser->parse($type),
                                    array_filter($reflectionType->getTypes(), [$this, 'shouldTypeHint']),
                                ),
                            ),
                        ],
                    ]);
                }
            } catch (ReflectionException $e) {
                continue;
            }
        }

        return $classMetadata;
    }

    private function getReflectionType(PropertyMetadata $propertyMetadata): ?ReflectionType
    {
        if ($this->isNotSupportedVirtualProperty($propertyMetadata)) {
            return null;
        }

        if ($propertyMetadata instanceof VirtualPropertyMetadata) {
            return (new ReflectionMethod($propertyMetadata->class, $propertyMetadata->getter))
                ->getReturnType();
        }

        return (new ReflectionProperty($propertyMetadata->class, $propertyMetadata->name))
            ->getType();
    }

    private function isNotSupportedVirtualProperty(PropertyMetadata $propertyMetadata): bool
    {
        return $propertyMetadata instanceof StaticPropertyMetadata
            || $propertyMetadata instanceof ExpressionPropertyMetadata;
    }

    /**
     * @phpstan-assert-if-true \ReflectionNamedType $reflectionType
     */
    private function shouldTypeHint(?ReflectionType $reflectionType): bool
    {
        if (!$reflectionType instanceof ReflectionNamedType) {
            return false;
        }

        if (in_array($reflectionType->getName(), $this->allowList, true)) {
            return true;
        }

        return class_exists($reflectionType->getName())
            || interface_exists($reflectionType->getName());
    }

    /**
     * @phpstan-assert-if-true \ReflectionUnionType $reflectionType
     */
    private function shouldTypeHintUnion(?ReflectionType $reflectionType)
    {
        if (!$reflectionType instanceof \ReflectionUnionType) {
            return false;
        }

        foreach ($reflectionType->getTypes() as $type) {
            if ($this->shouldTypeHint($type)) {
                return true;
            }
        }

        return false;
    }
}
