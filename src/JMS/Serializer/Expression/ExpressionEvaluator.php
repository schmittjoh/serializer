<?php

namespace JMS\Serializer\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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

    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function addContextVariable($name, $value)
    {
        $this->context[$name] = $value;
    }

    /**
     * @param string $expression
     * @param array $data
     */
    public function evaluate($expression, array $data = [])
    {
        return $this->expressionLanguage->evaluate($expression, array_merge($data, $this->context));
    }
}
