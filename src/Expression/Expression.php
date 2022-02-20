<?php

declare(strict_types=1);

namespace JMS\Serializer\Expression;

use Symfony\Component\ExpressionLanguage\ParsedExpression as BaseExpression;
use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class Expression implements \Serializable
{
    /**
     * @var BaseExpression
     */
    private $expression;

    public function __construct(BaseExpression $expression)
    {
        $this->expression = $expression;
    }

    public function getExpression(): BaseExpression
    {
        return $this->expression;
    }

    /**
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function __toString()
    {
        return (string) $this->expression;
    }

    /**
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function serialize()
    {
        return serialize([(string) $this->expression, serialize($this->expression->getNodes())]);
    }

    /**
     * @param string $str
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
     */
    public function unserialize($str): void
    {
        $this->expression = new SerializedParsedExpression(...unserialize($str));
    }

    public function __serialize(): array
    {
        return [(string) $this->expression, $this->expression->getNodes()];
    }

    public function __unserialize(array $data): void
    {
        [$expression, $nodes] = $data;
        $this->expression = new BaseExpression($expression, $nodes);
    }
}
