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
use ReflectionProperty;

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
    private $whiteList;

    /**
     * @param string[] $whiteList
     */
    public function __construct(DriverInterface $delegate, ?ParserInterface $typeParser = null, array $whiteList = [])
    {
        $this->delegate = $delegate;
        $this->typeParser = $typeParser ?: new Parser();
        $this->whiteList = array_merge($whiteList, $this->getDefaultWhiteList());
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

    public function loadMetadataForClass(ReflectionClass $class): ?ClassMetadata
    {
        $classMetadata = $this->delegate->loadMetadataForClass($class);
        \assert($classMetadata instanceof SerializerClassMetadata);

        if (null === $classMetadata) {
            return null;
        }

        // We base our scan on the internal driver's property list so that we
        // respect any internal white/blacklisting like in the AnnotationDriver
        foreach ($classMetadata->propertyMetadata as $key => $propertyMetadata) {
            // If the inner driver provides a type, don't guess anymore.
            if ($propertyMetadata->type || $this->isVirtualProperty($propertyMetadata)) {
                continue;
            }

            try {
                $propertyReflection = $this->getReflection($propertyMetadata);
                if ($this->shouldTypeHint($propertyReflection)) {
                    $type = $propertyReflection->getType()->getName();

                    $propertyMetadata->setType($this->typeParser->parse($type));
                }
            } catch (ReflectionException $e) {
                continue;
            }
        }

        return $classMetadata;
    }

    private function isVirtualProperty(PropertyMetadata $propertyMetadata): bool
    {
        return $propertyMetadata instanceof VirtualPropertyMetadata
            || $propertyMetadata instanceof StaticPropertyMetadata
            || $propertyMetadata instanceof ExpressionPropertyMetadata;
    }

    private function getReflection(PropertyMetadata $propertyMetadata): ReflectionProperty
    {
        return new ReflectionProperty($propertyMetadata->class, $propertyMetadata->name);
    }

    private function shouldTypeHint(ReflectionProperty $propertyReflection): bool
    {
        $reflectionType = $propertyReflection->getType();
        if (null === $reflectionType) {
            return false;
        }

        if (PHP_VERSION_ID >= 80000 && $reflectionType instanceof \ReflectionUnionType) {
            return false;
        }

        if (in_array($reflectionType->getName(), $this->whiteList, true)) {
            return true;
        }

        return class_exists($reflectionType->getName())
            || interface_exists($reflectionType->getName());
    }
}
