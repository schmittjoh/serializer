<?php

declare(strict_types=1);

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
    private $context = [];

    /**
     * @var array
     */
    private $cache = [];

    public function __construct(ExpressionLanguage $expressionLanguage, array $context = [], array $cache = [])
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->context = $context;
        $this->cache = $cache;
    }

    /**
     * @param mixed $value
     */
    public function setContextVariable(string $name, $value): void
    {
        $this->context[$name] = $value;
    }

    /**
     * @param  array $data
     *
     * @return mixed
     */
    public function evaluate(string $expression, array $data = [])
    {
        if (!\is_string($expression)) {
            return $expression;
        }

        $context = $data + $this->context;

        if (!array_key_exists($expression, $this->cache)) {
            $this->cache[$expression] = $this->expressionLanguage->parse($expression, array_keys($context));
        }

        return $this->expressionLanguage->evaluate($this->cache[$expression], $context);
    }
}
