<?php

declare(strict_types=1);

namespace JMS\Serializer\Expression;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface ExpressionEvaluatorInterface
{
    /**
     * @param  array $data
     *
     * @return mixed
     */
    public function evaluate(string $expression, array $data = []);
}
