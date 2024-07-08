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
     * In order to deserialize non-discriminated unions, each possible type is attempted in turn.
     * Therefore, the types must be ordered from most specific to least specific, so that the most specific type is attempted first.
     * 
     * ReflectionUnionType::getTypes() does not return types in that order, so we need to reorder them.
     *
     * This method reorders the types in the following order:
     *  - primitives in speficity order: null, true, false, int, float, bool, string
     *  - classes and interaces in order of most number of required properties
     */
    private function reorderTypes(array $type): array
    {
        $self = $this;
        if ($type['params']) {
            uasort($type['params'], function ($a, $b) use ($self) {
                if (\class_exists($a['name']) && \class_exists($b['name'])) {
                    $aMetadata = $self->loadMetadataForClass(new \ReflectionClass($a['name']));
                    $bMetadata = $self->loadMetadataForClass(new \ReflectionClass($b['name']));
                    $aRequiredPropertyCount = 0;
                    $bRequiredPropertyCount = 0;
                    foreach ($aMetadata->propertyMetadata as $propertyMetadata) {
                        if (!$self->allowsNull($propertyMetadata->type)) {
                            $aRequiredPropertyCount++;
                        }
                    }
                    foreach ($bMetadata->propertyMetadata as $propertyMetadata) {
                        if (!$self->allowsNull($propertyMetadata->type)) {
                            $bRequiredPropertyCount++;
                        }
                    }
                    return $bRequiredPropertyCount <=> $aRequiredPropertyCount;
                }
                if(\class_exists($a['name'])) {
                    return 1;
                }
                if(\class_exists($b['name'])) {
                    return -1;
                }
                $order = ['null' => 0, 'true' => 1, 'false' => 2, 'bool' => 3, 'int' => 4, 'float' => 5, 'string' => 6];

                return ($order[$a['name']] ?? 7) <=> ($order[$b['name']] ?? 7);
            });
        }

        return $type;
    }
    
    private function allowsNull(array $type) {
        $allowsNull = false;
        if ($type['name'] === 'union') {
            foreach($type['params'] as $param) {
                if ($param['name'] === 'NULL') {
                    $allowsNull = true;
                }
            }
        } elseif($type['name'] === 'NULL') {
            $allowsNull = true;
        }
        return $allowsNull;
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
                    $propertyMetadata->setType($this->reorderTypes([
                        'name' => 'union',
                        'params' => array_map(fn (string $type) => $this->typeParser->parse($type), $reflectionType->getTypes()),
                    ]));
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
