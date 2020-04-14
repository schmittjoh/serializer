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
            return $this->getType($type);
        } catch (Exception $e) {
            throw new SyntaxError($e->getMessage(), 0, $e);
        }
    }

    protected function getCatchablePatterns(): array
    {
        return [
            '(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*', // name
            '(\+|\-)?(0|[1-9]\d*)(\.\d+)?', // number
            'null',
            '""|\'\'', // empty string
            '"[^"]+"', // quoted string
            "'[^']+'", // apostrophed string
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
