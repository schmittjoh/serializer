<?php

declare(strict_types=1);

namespace JMS\Serializer\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class ExpressionEvaluator implements CompilableExpressionEvaluatorInterface, ExpressionEvaluatorInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var array
     */
    private $context = [];

    public function __construct(ExpressionLanguage $expressionLanguage, array $context = [])
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->context = $context;
    }

    /**
     * @param mixed $value
     */
    public function setContextVariable(string $name, $value): void
    {
        $this->context[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function evaluate(string $expression, array $data = [])
    {
        return $this->expressionLanguage->evaluate($expression, $data + $this->context);
    }

    /**
     * @return mixed
     */
    public function evaluateParsed(Expression $expression, array $data = [])
    {
        return $this->expressionLanguage->evaluate($expression->getExpression(), $data + $this->context);
    }

    public function parse(string $expression, array $names = []): Expression
    {
        return new Expression($this->expressionLanguage->parse($expression, array_merge(array_keys($this->context), $names)));
    }
}
