<?php

declare(strict_types=1);

namespace JMS\Serializer\Accessor;

use JMS\Serializer\Exception\ExpressionLanguageRequiredException;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;

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

    public function __construct(ExpressionEvaluatorInterface $evaluator = null)
    {
        $this->evaluator = $evaluator;
    }

    public function getValue(object $object, PropertyMetadata $metadata)
    {
        if ($metadata instanceof StaticPropertyMetadata) {
            return $metadata->getValue(null);
        }

        if ($metadata instanceof ExpressionPropertyMetadata) {
            if ($this->evaluator === null) {
                throw new ExpressionLanguageRequiredException(sprintf('The property %s on %s requires the expression accessor strategy to be enabled.', $metadata->name, $metadata->class));
            }

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

    public function setValue(object $object, $value, PropertyMetadata $metadata): void
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
