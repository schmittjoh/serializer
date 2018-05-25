<?php

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
