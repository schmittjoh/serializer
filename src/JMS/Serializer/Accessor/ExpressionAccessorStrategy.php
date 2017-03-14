<?php

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

use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class ExpressionAccessorStrategy implements AccessorStrategyInterface
{
    /**
     * @var AccessorStrategyInterface
     */
    private $fallback;
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $evaluator;

    public function __construct(ExpressionEvaluatorInterface $evaluator, AccessorStrategyInterface $fallback)
    {
        $this->fallback = $fallback;
        $this->evaluator = $evaluator;
    }

    public function getValue($object, PropertyMetadata $metadata)
    {
        if ($metadata instanceof ExpressionPropertyMetadata) {
            return $this->evaluator->evaluate($metadata->expression, array('object' => $object));
        }
        return $this->fallback->getValue($object, $metadata);
    }

    public function setValue($object, $value, PropertyMetadata $metadata)
    {
        $this->fallback->setValue($object, $value, $metadata);
    }
}
