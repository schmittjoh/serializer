<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\Type;

use JMS\Serializer\Type\Exception\SyntaxError;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /** @var ParserInterface */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new Parser();
    }

    /**
     * @dataProvider validTypesProvider
     */
    public function testParse(string $sourceType, array $expectedType): void
    {
        self::assertSame(
            $expectedType,
            $this->parser->parse($sourceType)
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
            'Foo<"""bar""">',
            $type('Foo', ['"bar"']),
        ];
        yield [
            "Foo<'a''b'>",
            $type('Foo', ["a'b"]),
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

        $this->parser->parse('');
    }

    public function testParamTypeMustEndWithBracket(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \"EOF\" (EOF) at line 1 and column 8:\n"
            . "Foo<bar\n"
            . '       ↑'
        );

        $this->parser->parse('Foo<bar');
    }

    public function testMustStartWithName(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \",\" (comma) at line 1 and column 1:\n"
            . ",\n"
            . '↑'
        );

        $this->parser->parse(',');
    }

    public function testEmptyParams(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \">\" (_parenthesis) at line 1 and column 5:\n"
            . "Foo<>\n"
            . '    ↑'
        );

        $this->parser->parse('Foo<>');
    }

    public function testNoTrailingComma(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \",\" (comma) at line 1 and column 7:\n"
            . "Foo<aa,>\n"
            . '      ↑'
        );

        $this->parser->parse('Foo<aa,>');
    }

    public function testLeadingBackslash(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unrecognized token \"\\\" at line 1 and column 5:\n"
            . "Foo<\Bar>\n"
            . '    ↑'
        );

        $this->parser->parse('Foo<\Bar>');
    }
}
