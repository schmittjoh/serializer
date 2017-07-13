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

namespace JMS\Serializer\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class ExpressionEvaluator implements ExpressionEvaluatorInterface
{

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var array
     */
    private $context = array();

    /**
     * @var array
     */
    private $cache = array();

    public function __construct(ExpressionLanguage $expressionLanguage, array $context = array(), array $cache = array())
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->context = $context;
        $this->cache = $cache;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setContextVariable($name, $value)
    {
        $this->context[$name] = $value;
    }

    /**
     * @param  string $expression
     * @param  array $data
     * @return mixed
     */
    public function evaluate($expression, array $data = array())
    {
        if (!is_string($expression)) {
            return $expression;
        }

        $context = $data + $this->context;

        if (!array_key_exists($expression, $this->cache)) {
            $this->cache[$expression] = $this->expressionLanguage->parse($expression, array_keys($context));
        }

        return $this->expressionLanguage->evaluate($this->cache[$expression], $context);
    }
}
