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
                    'comma' => ',',
                    'name' => '(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*',
                    'quote_:quoted_string' => '"',
                    'apostrophe_:apostrophed_string' => '\'',
                ],
                'quoted_string' => [
                    'quoted_string' => '(?:[^"]|"")+',
                    '_quote:default' => '"',
                ],
                'apostrophed_string' => [
                    'apostrophed_string' => '(?:[^\']|\'\')+',
                    '_apostrophe:default' => '\'',
                ],
            ],
            [
                'type' => new Choice('type', ['simple_type', 'compound_type'], null),
                1 => new Token(1, 'name', null, -1, true),
                2 => new Concatenation(2, [1], '#simple_type'),
                3 => new Token(3, 'quote_', null, -1, false),
                4 => new Token(4, 'quoted_string', null, -1, true),
                5 => new Token(5, '_quote', null, -1, false),
                6 => new Concatenation(6, [3, 4, 5], '#simple_type'),
                7 => new Token(7, 'apostrophe_', null, -1, false),
                8 => new Token(8, 'apostrophed_string', null, -1, true),
                9 => new Token(9, '_apostrophe', null, -1, false),
                10 => new Concatenation(10, [7, 8, 9], '#simple_type'),
                'simple_type' => new Choice('simple_type', [2, 6, 10], null),
                12 => new Token(12, 'name', null, -1, true),
                13 => new Token(13, 'parenthesis_', null, -1, false),
                14 => new Token(14, 'comma', null, -1, false),
                15 => new Concatenation(15, [14, 'type'], '#compound_type'),
                16 => new Repetition(16, 0, -1, 15, null),
                17 => new Token(17, '_parenthesis', null, -1, false),
                'compound_type' => new Concatenation('compound_type', [12, 13, 'type', 16, 17], null),
            ],
            []
        );

        $this->getRule('type')->setPPRepresentation(' simple_type() | compound_type()');
        $this->getRule('simple_type')->setDefaultId('#simple_type');
        $this->getRule('simple_type')->setPPRepresentation(' <name> | ::quote_:: <quoted_string> ::_quote:: | ::apostrophe_:: <apostrophed_string> ::_apostrophe::');
        $this->getRule('compound_type')->setDefaultId('#compound_type');
        $this->getRule('compound_type')->setPPRepresentation(' <name> ::parenthesis_:: type() ( ::comma:: type() )* ::_parenthesis::');
    }
}
