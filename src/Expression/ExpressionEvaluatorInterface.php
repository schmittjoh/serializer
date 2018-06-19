<?php

declare(strict_types=1);

namespace JMS\Serializer\Expression;

interface ExpressionEvaluatorInterface
{
    /**
     * @param  array $data
     * @return mixed
     */
    public function evaluate(string $expression, array $data = []);
}
