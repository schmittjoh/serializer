<?php

declare(strict_types=1);

namespace JMS\Serializer\Accessor;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\ExpressionLanguageRequiredException;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\UninitializedPropertyException;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class DefaultAccessorStrategy implements AccessorStrategyInterface
{
    /**
     * @var callable[]
     */
    private $readAccessors = [];

    /**
     * @var callable[]
     */
    private $writeAccessors = [];

    /**
     * @var \ReflectionProperty[]
     */
    private $propertyReflectionCache = [];

    /**
     * @var ExpressionEvaluatorInterface
     */
    private $evaluator;

    public function __construct(?ExpressionEvaluatorInterface $evaluator = null)
    {
        $this->evaluator = $evaluator;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(object $object, PropertyMetadata $metadata, SerializationContext $context)
    {
        if ($metadata instanceof StaticPropertyMetadata) {
            return $metadata->getValue();
        }

        if ($metadata instanceof ExpressionPropertyMetadata) {
            if (null === $this->evaluator) {
                throw new ExpressionLanguageRequiredException(sprintf('The property %s on %s requires the expression accessor strategy to be enabled.', $metadata->name, $metadata->class));
            }

            $variables = ['object' => $object, 'context' => $context, 'property_metadata' => $metadata];

            if (($metadata->expression instanceof Expression) && ($this->evaluator instanceof CompilableExpressionEvaluatorInterface)) {
                return $this->evaluator->evaluateParsed($metadata->expression, $variables);
            }

            return $this->evaluator->evaluate($metadata->expression, $variables);
        }

        if (null !== $metadata->getter) {
            return $object->{$metadata->getter}();
        }

        if ($metadata->forceReflectionAccess) {
            $ref = $this->propertyReflectionCache[$metadata->class][$metadata->name] ?? null;
            if (null === $ref) {
                $ref = new \ReflectionProperty($metadata->class, $metadata->name);
                $ref->setAccessible(true);
                $this->propertyReflectionCache[$metadata->class][$metadata->name] = $ref;
            }

            return $ref->getValue($object);
        }

        $accessor = $this->readAccessors[$metadata->class] ?? null;
        if (null === $accessor) {
            $accessor = \Closure::bind(static fn ($o, $name) => $o->$name, null, $metadata->class);
            $this->readAccessors[$metadata->class] = $accessor;
        }

        try {
            return $accessor($object, $metadata->name);
        } catch (\Error $e) {
            // handle uninitialized properties in PHP >= 7.4
            if (preg_match('/^Typed property ([\w\\\\@]+)::\$(\w+) must not be accessed before initialization$/', $e->getMessage(), $matches)) {
                throw new UninitializedPropertyException(sprintf('Uninitialized property "%s::$%s".', $metadata->class, $metadata->name), 0, $e);
            }

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(object $object, $value, PropertyMetadata $metadata, DeserializationContext $context): void
    {
        if (true === $metadata->readOnly) {
            throw new LogicException(sprintf('%s on %s is read only.', $metadata->name, $metadata->class));
        }

        if (null !== $metadata->setter) {
            $object->{$metadata->setter}($value);

            return;
        }

        if ($metadata->forceReflectionAccess) {
            $ref = $this->propertyReflectionCache[$metadata->class][$metadata->name] ?? null;
            if (null === $ref) {
                $ref = new \ReflectionProperty($metadata->class, $metadata->name);
                $ref->setAccessible(true);
                $this->propertyReflectionCache[$metadata->class][$metadata->name] = $ref;
            }

            $ref->setValue($object, $value);

            return;
        }

        $accessor = $this->writeAccessors[$metadata->class] ?? null;
        if (null === $accessor) {
            $accessor = \Closure::bind(static function ($o, $name, $value): void {
                $o->$name = $value;
            }, null, $metadata->class);
            $this->writeAccessors[$metadata->class] = $accessor;
        }

        $accessor($object, $metadata->name, $value);
    }
}
