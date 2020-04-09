<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\Type;

use JMS\Serializer\Type\Exception\SyntaxError;
use JMS\Serializer\Type\Lexer;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /** @var ParserInterface */
    private $lexer;

    protected function setUp(): void
    {
        $this->lexer = new Lexer();
    }

    /**
     * @dataProvider validTypesProvider
     */
    public function testParse(string $sourceType, array $expectedType): void
    {
        self::assertSame(
            $expectedType,
            $this->lexer->parse($sourceType)
        );
    }

    /**
     * @return mixed[][]
     */
    public function validTypesProvider(): iterable
    {
        $type = static function (string $name, array $params = []): array {
            return ['name' => $name, 'params' => $params];
        };

        yield [
            'string',
            $type('string'),
        ];
        yield [
            'array<Foo>',
            $type('array', [['name' => 'Foo', 'params' => []]]),
        ];
        yield [
            'Foo<\'a\'>',
            $type('Foo', ['a']),
        ];
        yield [
            'Foo<5>',
            $type('Foo', [5]),
        ];
        yield [
            'Foo<5.5>',
            $type('Foo', [5.5]),
        ];
        yield [
            'Foo<null>',
            $type('Foo', [null]),
        ];
        yield [
            'Foo<\'a\',\'b\',\'c\'>',
            $type('Foo', ['a', 'b', 'c']),
        ];
        yield [
            'Foo<\'a\',\'\'>',
            $type('Foo', ['a', '']),
        ];
        yield [
            'array<Foo,Bar>',
            $type('array', [['name' => 'Foo', 'params' => []], ['name' => 'Bar', 'params' => []]]),
        ];
        yield [
            'array<Foo\Bar, Baz\Boo>',
            $type('array', [['name' => 'Foo\Bar', 'params' => []], ['name' => 'Baz\Boo', 'params' => []]]),
        ];
        yield [
            'a<b<c,d>,e>',
            $type('a', [['name' => 'b', 'params' => [['name' => 'c', 'params' => []], ['name' => 'd', 'params' => []]]], ['name' => 'e', 'params' => []]]),

        ];
        yield [
            'Foo',
            $type('Foo'),
        ];
        yield [
            'Foo\Bar',
            $type('Foo\Bar'),
        ];
        yield [
            'Foo<"asdf asdf">',
            $type('Foo', ['asdf asdf']),
        ];
        yield [
            'Foo<[]>',
            $type('Foo', [[]]),
        ];
        yield [
            'Foo<[[]]>',
            $type('Foo', [[[]]]),
        ];
        yield [
            'Foo<[123]>',
            $type('Foo', [[123]]),
        ];
        yield [
            'Foo<[123, 456]>',
            $type('Foo', [[123, 456]]),
        ];
        yield [
            'Foo<[[123], 456, "bar"]>',
            $type('Foo', [[[123], 456, 'bar']]),
        ];
        yield [
            'DateTime<null, null, [\'Y-m-d\TH:i:s\', \'Y-m-d\TH:i:sP\']>',
            $type('DateTime', [null, null, ['Y-m-d\TH:i:s', 'Y-m-d\TH:i:sP']]),
        ];
    }

    public function testEmptyString(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \"EOF\" (EOF) at line 1 and column 1:\n"
            . "\n"
            . '↑'
        );

        $this->lexer->parse('');
    }

    public function testParamTypeMustEndWithBracket(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \"EOF\" (EOF) at line 1 and column 8:\n"
            . "Foo<bar\n"
            . '       ↑'
        );

        $this->lexer->parse('Foo<bar');
    }

    public function testMustStartWithName(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \",\" (comma) at line 1 and column 1:\n"
            . ",\n"
            . '↑'
        );

        $this->lexer->parse(',');
    }

    public function testEmptyParams(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \">\" (_parenthesis) at line 1 and column 5:\n"
            . "Foo<>\n"
            . '    ↑'
        );

        $this->lexer->parse('Foo<>');
    }

    public function testNoTrailingComma(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \",\" (comma) at line 1 and column 7:\n"
            . "Foo<aa,>\n"
            . '      ↑'
        );

        $this->lexer->parse('Foo<aa,>');
    }

    public function testLeadingBackslash(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unrecognized token \"\\\" at line 1 and column 5:\n"
            . "Foo<\Bar>\n"
            . '    ↑'
        );

        $this->lexer->parse('Foo<\Bar>');
    }
}
