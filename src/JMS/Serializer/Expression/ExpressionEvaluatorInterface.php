<?php

namespace JMS\Serializer\Expression;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface ExpressionEvaluatorInterface
{
    /**
     * @param  string $expression
     * @param  array $data
     * @return mixed
     */
    public function evaluate($expression, array $data = array());
}
