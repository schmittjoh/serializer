<?php

declare(strict_types=1);

namespace JMS\Serializer\Accessor;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class DefaultAccessorStrategy implements AccessorStrategyInterface
{
    private $readAccessors = [];
    private $writeAccessors = [];
    private $propertyReflectionCache = [];
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $evaluator;
    /**
     * @var ExclusionStrategyInterface
     */
    private $exclusionStrategy;
    /**
     * @var ExpressionLanguageExclusionStrategy
     */
    private $expressionExclusionStrategy;

    public function __construct(
        ExclusionStrategyInterface $exclusionStrategy,
        ExpressionEvaluatorInterface $evaluator
    ) {
        $this->exclusionStrategy = $exclusionStrategy;
        $this->evaluator = $evaluator;
        $this->expressionExclusionStrategy = new ExpressionLanguageExclusionStrategy($evaluator);
    }

    /**
     * @param ClassMetadata $metadata
     * @param Context $context
     * @return PropertyMetadata[]
     */
    public function getProperties(ClassMetadata $metadata, Context $context): array
    {
        $values = [];
        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            if ($context instanceof DeserializationContext && $propertyMetadata->readOnly) {
                continue;
            }

            if ($this->exclusionStrategy->shouldSkipProperty($propertyMetadata, $context) || $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $context)) {
                continue;
            }

            $values[] = $propertyMetadata;
        }

        return $values;
    }

    public function getValues(object $data, array $properties, SerializationContext $context): array
    {
        $shouldSerializeNull = $context->shouldSerializeNull();

        $values = [];
        foreach ($properties as $propertyMetadata) {

            $v = $this->getValue($data, $propertyMetadata, $context);

            if (null === $v && $shouldSerializeNull !== true) {
                continue;
            }

            $values[] = $v;
        }

        return $values;
    }

    /**
     * @param object $object
     * @param mixed[] $values
     * @param PropertyMetadata[] $properties
     * @param DeserializationContext $context
     * @return void
     */
    public function setValues(object $object, array $values, array $properties, DeserializationContext $context): void
    {
        $values = [];
        foreach ($properties as $i => $propertyMetadata) {

            if (!array_key_exists($i, $values)) {
                continue;
            }

            $this->setValue($object, $values[$i], $propertyMetadata, $context);
        }
    }

    private function getValue(object $object, PropertyMetadata $metadata, SerializationContext $context)
    {
        if ($metadata instanceof StaticPropertyMetadata) {
            return $metadata->getValue();
        }

        if ($metadata instanceof ExpressionPropertyMetadata) {
            return $this->evaluator->evaluate($metadata->expression, ['object' => $object]);
        }

        if (null === $metadata->getter) {
            if (!isset($this->readAccessors[$metadata->class])) {
                if ($metadata->forceReflectionAccess === true) {
                    $this->readAccessors[$metadata->class] = function ($o, $name) use ($metadata) {

                        $ref = $this->propertyReflectionCache[$metadata->class][$name] ?? null;
                        if ($ref === null) {
                            $ref = new \ReflectionProperty($metadata->class, $name);
                            $ref->setAccessible(true);
                            $this->propertyReflectionCache[$metadata->class][$name] = $ref;
                        }

                        return $ref->getValue($o);
                    };
                } else {
                    $this->readAccessors[$metadata->class] = \Closure::bind(function ($o, $name) {
                        return $o->$name;
                    }, null, $metadata->class);
                }
            }

            return $this->readAccessors[$metadata->class]($object, $metadata->name);
        }

        return $object->{$metadata->getter}();
    }

    public function setValue(object $object, $value, PropertyMetadata $metadata, DeserializationContext $context): void
    {
        if ($metadata->readOnly) {
            throw new LogicException(sprintf('%s on %s is read only.', $metadata->name, $metadata->class));
        }

        if (null === $metadata->setter) {
            if (!isset($this->writeAccessors[$metadata->class])) {
                if ($metadata->forceReflectionAccess === true) {
                    $this->writeAccessors[$metadata->class] = function ($o, $name, $value) use ($metadata) {
                        $ref = $this->propertyReflectionCache[$metadata->class][$name] ?? null;
                        if ($ref === null) {
                            $ref = new \ReflectionProperty($metadata->class, $name);
                            $ref->setAccessible(true);
                            $this->propertyReflectionCache[$metadata->class][$name] = $ref;
                        }

                        $ref->setValue($o, $value);
                    };
                } else {
                    $this->writeAccessors[$metadata->class] = \Closure::bind(function ($o, $name, $value) {
                        $o->$name = $value;
                    }, null, $metadata->class);
                }
            }

            $this->writeAccessors[$metadata->class]($object, $metadata->name, $value);
            return;
        }

        $object->{$metadata->setter}($value);
    }
}
