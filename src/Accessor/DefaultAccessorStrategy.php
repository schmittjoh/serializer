<?php

declare(strict_types=1);

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Accessor;

use JMS\Serializer\Exception\ExpressionLanguageRequiredException;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class DefaultAccessorStrategy implements AccessorStrategyInterface
{
    private $readAccessors = array();
    private $writeAccessors = array();

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
                $this->readAccessors[$metadata->class] = \Closure::bind(function ($o, $name) {
                    return $o->$name;
                }, null, $metadata->class);
            }

            return $this->readAccessors[$metadata->class]($object, $metadata->name);
        }

        return $object->{$metadata->getter}();
    }

    public function setValue(object $object, $value, PropertyMetadata $metadata): void
    {
        if ($metadata instanceof StaticPropertyMetadata || $metadata instanceof VirtualPropertyMetadata) {
            throw new LogicException(sprintf('%s on %s is immutable.'), $metadata->name, $metadata->class);
        }
        if ($metadata->readOnly) {
            throw new LogicException(sprintf('%s on %s is read only.'), $metadata->name, $metadata->class);
        }

        if (null === $metadata->setter) {
            if (!isset($this->writeAccessors[$metadata->class])) {
                $this->writeAccessors[$metadata->class] = \Closure::bind(function ($o, $name, $value) {
                    $o->$name = $value;
                }, null, $metadata->class);
            }

            $this->writeAccessors[$metadata->class]($object, $metadata->name, $value);
            return;
        }

        $object->{$metadata->setter}($value);
    }
}
