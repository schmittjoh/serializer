<?php

namespace JMS\Serializer\Type;

use Doctrine\Common\Lexer\AbstractLexer;
use Hoa\Exception\Exception;
use JMS\Serializer\Type\Exception\SyntaxError;

class Lexer extends AbstractLexer implements ParserInterface
{
    public function parse(string $type): array
    {
        try {
            // TODO: Implement parse() method.
        } catch (Exception $e) {
            throw new SyntaxError($e->getMessage(), 0, $e);
        }
    }

    protected function getCatchablePatterns()
    {
        return [
            // TODO: Implement getCatchablePatterns() method.
        ];
    }

    protected function getNonCatchablePatterns(): array
    {
        return [
            // TODO: Implement getNonCatchablePatterns() method.
        ];
    }

    protected function getType(&$value)
    {
        // TODO: Implement getType() method.
    }
}
