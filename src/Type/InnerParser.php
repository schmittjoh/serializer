<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

use Hoa\Compiler\Llk\Parser;
use Hoa\Compiler\Llk\Rule\Choice;
use Hoa\Compiler\Llk\Rule\Concatenation;
use Hoa\Compiler\Llk\Rule\Repetition;
use Hoa\Compiler\Llk\Rule\Token;

/**
 * @internal
 *
 * @generated Use regenerate-parser.php to refresh this class.
 */
final class InnerParser extends Parser
{
    public function __construct()
    {
        parent::__construct(
            [
                'default' => [
                    'skip' => '\s+',
                    'array_' => '\[',
                    '_array' => '\]',
                    'parenthesis_' => '<',
                    '_parenthesis' => '>',
                    'empty_string' => '""|\'\'',
                    'number' => '(\+|\-)?(0|[1-9]\d*)(\.\d+)?',
                    'null' => 'null',
                    'comma' => ',',
                    'name' => '(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*',
                    'quote_:quoted_string' => '"',
                    'apostrophe_:apostrophed_string' => '\'',
                ],
                'quoted_string' => [
                    'quoted_string' => '[^"]+',
                    '_quote:default' => '"',
                ],
                'apostrophed_string' => [
                    'apostrophed_string' => '[^\']+',
                    '_apostrophe:default' => '\'',
                ],
            ],
            [
                'type' => new Choice('type', ['simple_type', 'compound_type'], null),
                1 => new Token(1, 'name', null, -1, true),
                2 => new Concatenation(2, [1], '#simple_type'),
                3 => new Token(3, 'number', null, -1, true),
                4 => new Concatenation(4, [3], '#simple_type'),
                5 => new Token(5, 'null', null, -1, true),
                6 => new Concatenation(6, [5], '#simple_type'),
                7 => new Token(7, 'empty_string', null, -1, true),
                8 => new Concatenation(8, [7], '#simple_type'),
                9 => new Token(9, 'quote_', null, -1, false),
                10 => new Token(10, 'quoted_string', null, -1, true),
                11 => new Token(11, '_quote', null, -1, false),
                12 => new Concatenation(12, [9, 10, 11], '#simple_type'),
                13 => new Token(13, 'apostrophe_', null, -1, false),
                14 => new Token(14, 'apostrophed_string', null, -1, true),
                15 => new Token(15, '_apostrophe', null, -1, false),
                16 => new Concatenation(16, [13, 14, 15], '#simple_type'),
                17 => new Concatenation(17, ['array'], '#simple_type'),
                'simple_type' => new Choice('simple_type', [2, 4, 6, 8, 12, 16, 17], null),
                19 => new Token(19, 'name', null, -1, true),
                20 => new Token(20, 'parenthesis_', null, -1, false),
                21 => new Token(21, 'comma', null, -1, false),
                22 => new Concatenation(22, [21, 'type'], '#compound_type'),
                23 => new Repetition(23, 0, -1, 22, null),
                24 => new Token(24, '_parenthesis', null, -1, false),
                'compound_type' => new Concatenation('compound_type', [19, 20, 'type', 23, 24], null),
                26 => new Token(26, 'array_', null, -1, false),
                27 => new Token(27, 'comma', null, -1, false),
                28 => new Concatenation(28, [27, 'simple_type'], '#array'),
                29 => new Repetition(29, 0, -1, 28, null),
                30 => new Concatenation(30, ['simple_type', 29], null),
                31 => new Repetition(31, 0, 1, 30, null),
                32 => new Token(32, '_array', null, -1, false),
                'array' => new Concatenation('array', [26, 31, 32], null),
            ],
            []
        );

        $this->getRule('type')->setPPRepresentation(' simple_type() | compound_type()');
        $this->getRule('simple_type')->setDefaultId('#simple_type');
        $this->getRule('simple_type')->setPPRepresentation(' <name> | <number> | <null> | <empty_string> | ::quote_:: <quoted_string> ::_quote:: | ::apostrophe_:: <apostrophed_string> ::_apostrophe:: | array()');
        $this->getRule('compound_type')->setDefaultId('#compound_type');
        $this->getRule('compound_type')->setPPRepresentation(' <name> ::parenthesis_:: type() ( ::comma:: type() )* ::_parenthesis::');
        $this->getRule('array')->setDefaultId('#array');
        $this->getRule('array')->setPPRepresentation(' ::array_:: ( simple_type() ( ::comma:: simple_type() )* )? ::_array::');
    }
}
