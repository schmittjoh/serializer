<?php

namespace JMS\Serializer\Expression;

interface ExpressionEvaluatorInterface
{
    /**
     * @param string $name
     * @param mixed $value
     */
    public function addContextVariable($name, $value);

    /**
     * @param string $expression
     * @param array $data
     */
    public function evaluate($expression, array $data = []);
}
