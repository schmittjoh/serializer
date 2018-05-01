<?php

declare(strict_types=1);

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests\Serializer\Type;

use JMS\Serializer\Type\Exception\SyntaxError;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var ParserInterface */
    private $parser;

    protected function setUp() : void
    {
        $this->parser = new Parser();
    }

    /**
     * @dataProvider validTypesProvider
     */
    public function testParse(string $sourceType, array $expectedType) : void
    {
        self::assertSame(
            $expectedType,
            $this->parser->parse($sourceType)
        );
    }

    /**
     * @return mixed[][]
     */
    public function validTypesProvider() : iterable
    {
        $type = function (string $name, array $params = []) : array {
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
            $type('array', [['name' => 'Foo', 'params' => []], ['name' => 'Bar', 'params' => []]])
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

    public function testEmptyString() : void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \"EOF\" (EOF) at line 1 and column 1:\n"
            . "\n"
            . "↑");

        $this->parser->parse('');
    }

    public function testParamTypeMustEndWithBracket() : void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \"EOF\" (EOF) at line 1 and column 8:\n"
            . "Foo<bar\n"
            . "       ↑");

        $this->parser->parse('Foo<bar');
    }

    public function testMustStartWithName() : void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \",\" (comma) at line 1 and column 1:\n"
            . ",\n"
            . "↑");

        $this->parser->parse(',');
    }

    public function testEmptyParams() : void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \">\" (_parenthesis) at line 1 and column 5:\n"
            . "Foo<>\n"
            . "    ↑");

        $this->parser->parse('Foo<>');
    }

    public function testNoTrailingComma() : void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unexpected token \",\" (comma) at line 1 and column 7:\n"
            . "Foo<aa,>\n"
            . "      ↑");

        $this->parser->parse('Foo<aa,>');
    }

    public function testLeadingBackslash() : void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage(
            "Unrecognized token \"\\\" at line 1 and column 5:\n"
            . "Foo<\Bar>\n"
            . "    ↑");

        $this->parser->parse('Foo<\Bar>');
    }
}
