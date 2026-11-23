<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

use JMS\Serializer\Type\Exception\SyntaxError;

/**
 * @internal
 *
 * @phpstan-import-type TypeArray from Type
 */
final class Parser implements ParserInterface
{
    /**
     * @var Lexer
     */
    private $lexer;

    public function parse(string $string): array
    {
        $this->lexer = new Lexer();
        $this->lexer->setInput($string);
        $this->lexer->moveNext();

        $type = $this->visit(true);

        if (null !== $this->lexer->lookahead) {
            throw new SyntaxError(sprintf(
                'Syntax error, unexpected "%s" (%s)',
                $this->lexer->lookahead->value,
                $this->getConstant($this->lexer->lookahead->type),
            ));
        }

        return $type;
    }

    /**
     * @return mixed
     */
    private function visit(bool $root = false)
    {
        $this->lexer->moveNext();

        if (!$this->lexer->token) {
            throw new SyntaxError(
                'Syntax error, unexpected end of stream',
            );
        }

        if (Lexer::T_IDENTIFIER === $this->lexer->token->type) {
            if ($this->lexer->isNextToken(Lexer::T_TYPE_START)) {
                return $this->visitCompoundType();
            } elseif (!$root && $this->lexer->isNextToken(Lexer::T_ARRAY_START)) {
                return $this->visitArrayType();
            }

            return $this->visitSimpleType();
        }

        if (!$root) {
            switch ($this->lexer->token->type) {
                case Lexer::T_FLOAT:
                    return floatval($this->lexer->token->value);

                case Lexer::T_INTEGER:
                    return intval($this->lexer->token->value);

                case Lexer::T_NULL:
                    return null;

                case Lexer::T_STRING:
                    return $this->lexer->token->value;

                case Lexer::T_ARRAY_START:
                    return $this->visitArrayType();
            }
        }

        throw new SyntaxError(sprintf(
            'Syntax error, unexpected "%s" (%s)',
            $this->lexer->token->value,
            $this->getConstant($this->lexer->token->type),
        ));
    }

    /**
     * @return TypeArray
     */
    private function visitSimpleType(): array
    {
        $value = $this->lexer->token->value;

        return ['name' => $value, 'params' => []];
    }

    /**
     * @return TypeArray
     */
    private function visitCompoundType(): array
    {
        $name = $this->lexer->token->value;
        $this->match(Lexer::T_TYPE_START);

        $params = [];
        if (!$this->lexer->isNextToken(Lexer::T_TYPE_END)) {
            while (true) {
                $params[] = $this->visit();

                if ($this->lexer->isNextToken(Lexer::T_TYPE_END)) {
                    break;
                }

                $this->match(Lexer::T_COMMA);
            }
        }

        $this->match(Lexer::T_TYPE_END);

        return [
            'name' => $name,
            'params' => $params,
        ];
    }

    private function visitArrayType(): array
    {
        /*
         * Here we should call $this->match(Lexer::T_ARRAY_START); to make it clean
         * but the token has already been consumed by moveNext() in visit()
         */

        $params = [];
        if (!$this->lexer->isNextToken(Lexer::T_ARRAY_END)) {
            while (true) {
                $params[] = $this->visit();
                if ($this->lexer->isNextToken(Lexer::T_ARRAY_END)) {
                    break;
                }

                $this->match(Lexer::T_COMMA);
            }
        }

        $this->match(Lexer::T_ARRAY_END);

        return $params;
    }

    private function match(int $token): void
    {
        if (!$this->lexer->lookahead) {
            throw new SyntaxError(
                sprintf('Syntax error, unexpected end of stream, expected %s', $this->getConstant($token)),
            );
        }

        if ($this->lexer->lookahead->type === $token) {
            $this->lexer->moveNext();

            return;
        }

        throw new SyntaxError(sprintf(
            'Syntax error, unexpected "%s" (%s), expected was %s',
            $this->lexer->lookahead->value,
            $this->getConstant($this->lexer->lookahead->type),
            $this->getConstant($token),
        ));
    }

    private function getConstant(int $value): string
    {
        $oClass = new \ReflectionClass(Lexer::class);

        return array_search($value, $oClass->getConstants());
    }
}
