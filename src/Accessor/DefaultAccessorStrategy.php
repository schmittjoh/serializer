<?php

declare(strict_types=1);

namespace JMS\Serializer\Accessor;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\ExpressionLanguageRequiredException;
use JMS\Serializer\Exception\LogicException;
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

        if (!isset($this->readAccessors[$metadata->class][$metadata->name])) {
            if ($metadata->forceReflectionAccess) {
                $accessor = static function ($object, $name) use ($metadata) {
                    return $metadata->reflection->getValue($object);
                };
            } else {
                $accessor = \Closure::bind(static function ($object, $name) {
                    return $object->$name;
                }, null, $metadata->class);
            }

            $this->readAccessors[$metadata->class][$metadata->name] = $accessor;
        }

        return $this->readAccessors[$metadata->class][$metadata->name]($object, $metadata->name);
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

        if (!isset($this->writeAccessors[$metadata->class][$metadata->name])) {
            if ($metadata->forceReflectionAccess) {
                $accessor = static function ($object, $name, $value) use ($metadata): void {
                    $metadata->reflection->setValue($object, $value);
                };
            } else {
                $accessor = \Closure::bind(static function ($object, $name, $value): void {
                    $object->$name = $value;
                }, null, $metadata->class);
            }
            $this->writeAccessors[$metadata->class][$metadata->name] = $accessor;
        }

        $this->writeAccessors[$metadata->class][$metadata->name]($object, $metadata->name, $value);
    }
}
