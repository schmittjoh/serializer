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
            'Foo<\'a\'>',
            $type('Foo', ['a']),
        ];

        yield [
            'Foo<>',
            $type('Foo', []),
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

    /**
     * @dataProvider wrongSyntax
     */
    public function testSyntaxError($value): void
    {
        $this->expectException(SyntaxError::class);
        $this->parser->parse($value);
    }

    public function wrongSyntax()
    {
        return [
            ['Foo<\Bar>]'],
            ['Foo<aa,>'],
            ['Foo<bar'],
            [''],
            [','],
            ['[]'],
            ['<>'],
        ];
    }
}
