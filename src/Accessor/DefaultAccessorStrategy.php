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
            return $metadata->getValue(null);
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

        if (null === $metadata->getter) {
            if (!isset($this->readAccessors[$metadata->class])) {
                if (true === $metadata->forceReflectionAccess) {
                    $this->readAccessors[$metadata->class] = function ($o, $name) use ($metadata) {
                        $ref = $this->propertyReflectionCache[$metadata->class][$name] ?? null;
                        if (null === $ref) {
                            $ref = new \ReflectionProperty($metadata->class, $name);
                            $ref->setAccessible(true);
                            $this->propertyReflectionCache[$metadata->class][$name] = $ref;
                        }

                        return $ref->getValue($o);
                    };
                } else {
                    $this->readAccessors[$metadata->class] = \Closure::bind(static function ($o, $name) {
                        return $o->$name;
                    }, null, $metadata->class);
                }
            }

            return $this->readAccessors[$metadata->class]($object, $metadata->name);
        }

        return $object->{$metadata->getter}();
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(object $object, $value, PropertyMetadata $metadata, DeserializationContext $context): void
    {
        if (true === $metadata->readOnly) {
            throw new LogicException(sprintf('%s on %s is read only.', $metadata->name, $metadata->class));
        }

        if (null === $metadata->setter) {
            if (!isset($this->writeAccessors[$metadata->class])) {
                if (true === $metadata->forceReflectionAccess) {
                    $this->writeAccessors[$metadata->class] = function ($o, $name, $value) use ($metadata): void {
                        $ref = $this->propertyReflectionCache[$metadata->class][$name] ?? null;
                        if (null === $ref) {
                            $ref = new \ReflectionProperty($metadata->class, $name);
                            $ref->setAccessible(true);
                            $this->propertyReflectionCache[$metadata->class][$name] = $ref;
                        }

                        $ref->setValue($o, $value);
                    };
                } else {
                    $this->writeAccessors[$metadata->class] = \Closure::bind(static function ($o, $name, $value): void {
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
