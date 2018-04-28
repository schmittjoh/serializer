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

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\TypeParser;

class TypeParserTest extends \PHPUnit\Framework\TestCase
{
    private $parser;

    /**
     * @dataProvider getTypes
     */
    public function testParse($type, $name, array $params = [])
    {
        $this->assertEquals(['name' => $name, 'params' => $params], $this->parser->parse($type));
    }

    public function getTypes()
    {
        $types = [];
        $types[] = ['string', 'string'];
        $types[] = ['array<Foo>', 'array', [['name' => 'Foo', 'params' => []]]];
        $types[] = ['array<Foo,Bar>', 'array', [['name' => 'Foo', 'params' => []], ['name' => 'Bar', 'params' => []]]];
        $types[] = ['array<Foo\Bar, Baz\Boo>', 'array', [['name' => 'Foo\Bar', 'params' => []], ['name' => 'Baz\Boo', 'params' => []]]];
        $types[] = ['a<b<c,d>,e>', 'a', [['name' => 'b', 'params' => [['name' => 'c', 'params' => []], ['name' => 'd', 'params' => []]]], ['name' => 'e', 'params' => []]]];
        $types[] = ['Foo', 'Foo'];
        $types[] = ['Foo\Bar', 'Foo\Bar'];
        $types[] = ['Foo<"asdf asdf">', 'Foo', ['asdf asdf']];

        return $types;
    }

    /**
     * @expectedException \JMS\Parser\SyntaxErrorException
     * @expectedExceptionMessage Expected T_CLOSE_BRACKET, but got end of input.
     */
    public function testParamTypeMustEndWithBracket()
    {
        $this->parser->parse('Foo<bar');
    }

    /**
     * @expectedException \JMS\Parser\SyntaxErrorException
     * @expectedExceptionMessage Expected T_NAME, but got "," of type T_COMMA at beginning of input.
     */
    public function testMustStartWithName()
    {
        $this->parser->parse(',');
    }

    /**
     * @expectedException \JMS\Parser\SyntaxErrorException
     * @expectedExceptionMessage Expected any of T_NAME or T_STRING, but got ">" of type T_CLOSE_BRACKET at position 4 (0-based).
     */
    public function testEmptyParams()
    {
        $this->parser->parse('Foo<>');
    }

    /**
     * @expectedException \JMS\Parser\SyntaxErrorException
     * @expectedExceptionMessage Expected any of T_NAME or T_STRING, but got ">" of type T_CLOSE_BRACKET at position 7 (0-based).
     */
    public function testNoTrailingComma()
    {
        $this->parser->parse('Foo<aa,>');
    }

    /**
     * @expectedException \JMS\Parser\SyntaxErrorException
     * @expectedExceptionMessage  Expected any of T_NAME or T_STRING, but got "\" of type T_NONE at position 4 (0-based).
     */
    public function testLeadingBackslash()
    {
        $this->parser->parse('Foo<\Bar>');
    }

    protected function setUp()
    {
        $this->parser = new TypeParser();
    }
}
