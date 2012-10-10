<?php

namespace JMS\SerializerBundle\Serializer;

/**
 * Parses a serializer type.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class TypeParser
{
    const T_NAME = 1;
    const T_STRING = 2;
    const T_OPEN_BRACKET = 3;
    const T_CLOSE_BRACKET = 4;
    const T_COMMA = 5;
    const T_NONE = 6;

    private $tokens;
    private $token;
    private $next;
    private $pointer = 0;

    /**
     * @param string $type
     *
     * @return array of the format ["name" => string, "params" => array]
     */
    public function parse($type)
    {
        if (empty($type)) {
            throw new \InvalidArgumentException('$type cannot be empty.');
        }

        $this->tokenize($type);

        $parsedType = $this->parseType();
        if (null !== $this->next) {
            throw new \InvalidArgumentException(sprintf('Expected end of type, but got %s (%s) at position %d.', $this->getTokenName($this->next[0]), json_encode($this->next[2]), $this->next[1]));
        }

        return $parsedType;
    }

    private function parseType()
    {
        $this->match(self::T_NAME);
        $typeName = $this->token[0];

        if ( ! $this->isNextToken(self::T_OPEN_BRACKET)) {
            return array('name' => $typeName, 'params' => array());
        }

        $this->match(self::T_OPEN_BRACKET);
        $params = array();
        do {
            if ($this->isNextToken(self::T_NAME)) {
                $params[] = $this->parseType();
            } else if ($this->isNextToken(self::T_STRING)) {
                $this->moveNext();
                $params[] = $this->token[0];
            } else {
                $this->matchAny(array(self::T_NAME, self::T_STRING)); // Will throw an exception.
            }
        } while ($this->isNextToken(self::T_COMMA) && $this->moveNext());

        $this->match(self::T_CLOSE_BRACKET);

        return array('name' => $typeName, 'params' => $params);
    }

    private function isNextToken($token)
    {
        return null !== $this->next && $this->next[2] === $token;
    }

    private function matchAny(array $tokens)
    {
        if (null === $this->next) {
            throw new \InvalidArgumentException(sprintf('Expected any of %s, but reached end of type.', implode(' or ', array_map(array($this, 'getTokenName'), $tokens))));
        }

        $found = false;
        foreach ($tokens as $token) {
            if ($this->next[2] === $token) {
                $found = true;
                break;
            }
        }

        if ( ! $found) {
            throw new \InvalidArgumentException(sprintf('Expected any of %s, but got %s at position %d.', implode(' or ', array_map(array($this, 'getTokenName'), $tokens)), $this->getTokenName($this->next[2]), $this->next[1]));
        }

        $this->moveNext();
    }

    private function match($token)
    {
        if (null === $this->next) {
            throw new \InvalidArgumentException(sprintf('Expected token %s, but reached end of type.', $this->getTokenName($token)));
        }

        if ($this->next[2] !== $token) {
            throw new \InvalidArgumentException(sprintf('Expected token %s, but got %s at position %d.', $this->getTokenName($token), $this->getTokenName($this->next[2]), $this->next[1]));
        }

        $this->moveNext();
    }

    private function moveNext()
    {
        $this->pointer += 1;
        $this->token = $this->next;
        $this->next = isset($this->tokens[$this->pointer]) ? $this->tokens[$this->pointer] : null;

        return null !== $this->next;
    }

    public static function getTokenName($token)
    {
        $ref = new \ReflectionClass(get_called_class());
        foreach ($ref->getConstants() as $name => $value) {
            if ($value === $token) {
                return $name;
            }
        }

        throw new \LogicException(sprintf('The token %s does not exist.', json_encode($token)));
    }

    private function tokenize($type)
    {
        $this->tokens = preg_split('/((?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*|"(?:[^"]|"")*"|\'(?:[^\']|\'\')*\'|<|>|,)|\s*/', $type, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);
        $this->pointer = -1;

        foreach ($this->tokens as &$token) {
            $token[2] = $this->getType($token[0]);
        }

        $this->moveNext();
    }

    private function getType(&$value)
    {
        switch ($value[0]) {
            case '"':
            case "'":
                $value = substr($value, 1, -1);

                return self::T_STRING;

            case '<':
                return self::T_OPEN_BRACKET;

            case '>':
                return self::T_CLOSE_BRACKET;

            case ',':
                return self::T_COMMA;

            default:
                if (preg_match('/^(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $value)) {
                    return self::T_NAME;
                }

                return self::T_NONE;
        }
    }
}