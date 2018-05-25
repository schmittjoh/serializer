<?php

namespace JMS\Serializer;

/**
 * Parses a serializer type.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class TypeParser extends \JMS\Parser\AbstractParser
{
    const T_NAME = 1;
    const T_STRING = 2;
    const T_OPEN_BRACKET = 3;
    const T_CLOSE_BRACKET = 4;
    const T_COMMA = 5;
    const T_NONE = 6;

    public function __construct()
    {
        parent::__construct(new \JMS\Parser\SimpleLexer(
            '/
                # PHP Class Names
                ((?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)

                # Strings
                |("(?:[^"]|"")*"|\'(?:[^\']|\'\')*\')

                # Ignore whitespace
                |\s*

                # Terminals
                |(.)
            /x',
            array(self::T_NAME => 'T_NAME', self::T_STRING => 'T_STRING', self::T_OPEN_BRACKET => 'T_OPEN_BRACKET',
                self::T_CLOSE_BRACKET => 'T_CLOSE_BRACKET', self::T_COMMA => 'T_COMMA', self::T_NONE => 'T_NONE'),
            function ($value) {
                switch ($value[0]) {
                    case '"':
                    case "'":
                        return array(TypeParser::T_STRING, substr($value, 1, -1));

                    case '<':
                        return array(TypeParser::T_OPEN_BRACKET, '<');

                    case '>':
                        return array(TypeParser::T_CLOSE_BRACKET, '>');

                    case ',':
                        return array(TypeParser::T_COMMA, ',');

                    default:
                        if (preg_match('/^(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $value)) {
                            return array(TypeParser::T_NAME, $value);
                        }

                        return array(TypeParser::T_NONE, $value);
                }
            }
        ));
    }

    /**
     * @return array of the format ["name" => string, "params" => array]
     */
    protected function parseInternal()
    {
        $typeName = $this->match(self::T_NAME);
        if (!$this->lexer->isNext(self::T_OPEN_BRACKET)) {
            return array('name' => $typeName, 'params' => array());
        }

        $this->match(self::T_OPEN_BRACKET);
        $params = array();
        do {
            if ($this->lexer->isNext(self::T_NAME)) {
                $params[] = $this->parseInternal();
            } else if ($this->lexer->isNext(self::T_STRING)) {
                $params[] = $this->match(self::T_STRING);
            } else {
                $this->matchAny(array(self::T_NAME, self::T_STRING)); // Will throw an exception.
            }
        } while ($this->lexer->isNext(self::T_COMMA) && $this->lexer->moveNext());

        $this->match(self::T_CLOSE_BRACKET);

        return array('name' => $typeName, 'params' => $params);
    }
}
