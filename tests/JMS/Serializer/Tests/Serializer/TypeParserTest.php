<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

class TypeParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;

    /**
     * @dataProvider getTypes
     */
    public function testParse($type, $name, array $params = array())
    {
        $this->assertEquals(array('name' => $name, 'params' => $params), $this->parser->parse($type));
    }

    public function getTypes()
    {
        $types = array();
        $types[] = array('string', 'string');
        $types[] = array('array<Foo>', 'array', array(array('name' => 'Foo', 'params' => array())));
        $types[] = array('array<Foo,Bar>', 'array', array(array('name' => 'Foo', 'params' => array()), array('name' => 'Bar', 'params' => array())));
        $types[] = array('array<Foo\Bar, Baz\Boo>', 'array', array(array('name' => 'Foo\Bar', 'params' => array()), array('name' => 'Baz\Boo', 'params' => array())));
        $types[] = array('a<b<c,d>,e>', 'a', array(array('name' => 'b', 'params' => array(array('name' => 'c', 'params' => array()), array('name' => 'd', 'params' => array()))), array('name' => 'e', 'params' => array())));
        $types[] = array('Foo', 'Foo');
        $types[] = array('Foo\Bar', 'Foo\Bar');
        $types[] = array('Foo<"asdf asdf">', 'Foo', array('asdf asdf'));

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
