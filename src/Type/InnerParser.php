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
                'simple_type' => new Choice('simple_type', [2, 4, 6, 8, 12, 16], null),
                18 => new Token(18, 'name', null, -1, true),
                19 => new Token(19, 'parenthesis_', null, -1, false),
                20 => new Token(20, 'comma', null, -1, false),
                21 => new Concatenation(21, [20, 'type'], '#compound_type'),
                22 => new Repetition(22, 0, -1, 21, null),
                23 => new Token(23, '_parenthesis', null, -1, false),
                'compound_type' => new Concatenation('compound_type', [18, 19, 'type', 22, 23], null),
            ],
            []
        );

        $this->getRule('type')->setPPRepresentation(' simple_type() | compound_type()');
        $this->getRule('simple_type')->setDefaultId('#simple_type');
        $this->getRule('simple_type')->setPPRepresentation(' <name> | <number> | <null> | <empty_string> | ::quote_:: <quoted_string> ::_quote:: | ::apostrophe_:: <apostrophed_string> ::_apostrophe::');
        $this->getRule('compound_type')->setDefaultId('#compound_type');
        $this->getRule('compound_type')->setPPRepresentation(' <name> ::parenthesis_:: type() ( ::comma:: type() )* ::_parenthesis::');
    }
}
