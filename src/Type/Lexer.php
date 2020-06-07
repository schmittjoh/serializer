<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

use Doctrine\Common\Lexer\AbstractLexer;
use JMS\Serializer\Type\Exception\SyntaxError;

/**
 * @internal
 */
final class Lexer extends AbstractLexer implements ParserInterface
{
    public const T_UNKNOWN = 0;
    public const T_INTEGER = 1;
    public const T_STRING = 2;
    public const T_FLOAT = 3;
    public const T_ARRAY_START = 4;
    public const T_ARRAY_END = 5;
    public const T_COMMA = 6;
    public const T_TYPE_START = 7;
    public const T_TYPE_END = 8;
    public const T_IDENTIFIER = 9;
    public const T_NULL = 10;

    public function parse(string $type): array
    {
        try {
            return $this->getType($type);
        } catch (\Throwable $e) {
            throw new SyntaxError($e->getMessage(), 0, $e);
        }
    }

    protected function getCatchablePatterns(): array
    {
        return [
            '[a-z][a-z_\\\\0-9]*', // identifier or qualified name
            "'(?:[^']|'')*'", // single quoted strings
            '(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?', // numbers
            '"(?:[^"]|"")*"', // double quoted strings
            '<',
            '>',
            '\\[',
            '\\]',
        ];
    }

    protected function getNonCatchablePatterns(): array
    {
        return ['\s+'];
    }

    /**
     * {{@inheritDoc}}
     */
    protected function getType(&$value)
    {
        $type = self::T_UNKNOWN;

        switch (true) {
            // Recognize numeric values
            case is_numeric($value):
                if (false !== strpos($value, '.') || false !== stripos($value, 'e')) {
                    return self::T_FLOAT;
                }

                return self::T_INTEGER;

            // Recognize quoted strings
            case "'" === $value[0]:
                $value = str_replace("''", "'", substr($value, 1, strlen($value) - 2));

                return self::T_STRING;
            case '"' === $value[0]:
                $value = str_replace('""', '"', substr($value, 1, strlen($value) - 2));

                return self::T_STRING;
            case 'null' === $value:
                return self::T_NULL;
            // Recognize identifiers, aliased or qualified names
            case ctype_alpha($value[0]) || '\\' === $value[0]:
                return self::T_IDENTIFIER;
            case ',' === $value:
                return self::T_COMMA;
            case '>' === $value:
                return self::T_TYPE_END;
            case '<' === $value:
                return self::T_TYPE_START;
            case ']' === $value:
                return self::T_ARRAY_END;
            case '[' === $value:
                return self::T_ARRAY_START;

            // Default
            default:
                // Do nothing
        }

        return $type;
    }
}
