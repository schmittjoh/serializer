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
                    'parenthesis_' => '<',
                    '_parenthesis' => '>',
                    'empty_string' => '""|\'\'',
                    'number' => '(\+|\-)?(0|[1-9]\d*)(\.\d+)?',
                    'null' => 'null',
                    'comma' => ',',
                    'name' => '(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*',
                    'nullable' => '\?',
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
                1 => new Token(1, 'nullable', null, -1, true),
                2 => new Repetition(2, 0, 1, 1, null),
                3 => new Concatenation(3, [2], '#simple_type'),
                4 => new Token(4, 'name', null, -1, true),
                5 => new Concatenation(5, [3, 4], null),
                6 => new Token(6, 'number', null, -1, true),
                7 => new Concatenation(7, [6], '#simple_type'),
                8 => new Token(8, 'null', null, -1, true),
                9 => new Concatenation(9, [8], '#simple_type'),
                10 => new Token(10, 'empty_string', null, -1, true),
                11 => new Concatenation(11, [10], '#simple_type'),
                12 => new Token(12, 'quote_', null, -1, false),
                13 => new Token(13, 'quoted_string', null, -1, true),
                14 => new Token(14, '_quote', null, -1, false),
                15 => new Concatenation(15, [12, 13, 14], '#simple_type'),
                16 => new Token(16, 'apostrophe_', null, -1, false),
                17 => new Token(17, 'apostrophed_string', null, -1, true),
                18 => new Token(18, '_apostrophe', null, -1, false),
                19 => new Concatenation(19, [16, 17, 18], '#simple_type'),
                'simple_type' => new Choice('simple_type', [5, 7, 9, 11, 15, 19], null),
                21 => new Token(21, 'nullable', null, -1, true),
                22 => new Repetition(22, 0, 1, 21, null),
                23 => new Concatenation(23, [22], '#compound_type'),
                24 => new Token(24, 'name', null, -1, true),
                25 => new Token(25, 'parenthesis_', null, -1, false),
                26 => new Token(26, 'comma', null, -1, false),
                27 => new Concatenation(27, [26, 'type'], null),
                28 => new Repetition(28, 0, -1, 27, null),
                29 => new Token(29, '_parenthesis', null, -1, false),
                'compound_type' => new Concatenation('compound_type', [23, 24, 25, 'type', 28, 29], null),
            ],
            []
        );

        $this->getRule('type')->setPPRepresentation(' simple_type() | compound_type()');
        $this->getRule('simple_type')->setDefaultId('#simple_type');
        $this->getRule('simple_type')->setPPRepresentation(' (<nullable>?) <name> | <number> | <null> | <empty_string> | ::quote_:: <quoted_string> ::_quote:: | ::apostrophe_:: <apostrophed_string> ::_apostrophe::');
        $this->getRule('compound_type')->setDefaultId('#compound_type');
        $this->getRule('compound_type')->setPPRepresentation(' (<nullable>?) <name> ::parenthesis_:: type() ( ::comma:: type() )* ::_parenthesis::');
    }
}
