<?php

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializationContext;

class YamlSerializationTest extends BaseSerializationTest
{
    public function testObjectUsingTypeCasting()
    {
        $this->markTestSkipped('This is not available for the YAML format.');
    }

    public function testEmptyChild()
    {
        $this->markTestSkipped('This is not available for the YAML format.');
    }

    public function testSkipEmptyChild()
    {
        $this->markTestSkipped('This is not available for the YAML format.');
    }

    public function testConstraintViolation()
    {
        $this->markTestSkipped('This is not available for the YAML format.');
    }

    public function testConstraintViolationList()
    {
        $this->markTestSkipped('This is not available for the YAML format.');
    }

    public function testFormErrors()
    {
        $this->markTestSkipped('This is not available for the YAML format.');
    }

    public function testNestedFormErrors()
    {
        $this->markTestSkipped('This is not available for the YAML format.');
    }

    public function testFormErrorsWithNonFormComponents()
    {
        $this->markTestSkipped('This is not available for the YAML format.');
    }

    protected function getContent($key)
    {
        if (!file_exists($file = __DIR__ . '/yml/' . $key . '.yml')) {
            throw new RuntimeException(sprintf('The content with key "%s" does not exist.', $key));
        }

        return file_get_contents($file);
    }

    public function getTypeHintedArrays()
    {
        return [

            [[1, 2], "- 1\n- 2\n", null],
            [['a', 'b'], "- a\n- b\n", null],
            [['a' => 'a', 'b' => 'b'], "a: a\nb: b\n", null],
            [
                ['int' => 1, 'string' => 'a', 'dateTime' => new \DateTime('2018-07-07 10:10:10')],
                "int: 1\nstring: a\ndateTime: 2018-07-07 10:10:10\n",
                null
            ],

            [[], " []\n", null],
            [[], " []\n", SerializationContext::create()->setInitialType('array')],
            [[], " []\n", SerializationContext::create()->setInitialType('array<integer>')],
            [[], " {}\n", SerializationContext::create()->setInitialType('array<string,integer>')],
            [[], " {}\n", SerializationContext::create()->setInitialType('array<string,mixed>')],


            [[1, 2], "- 1\n- 2\n", SerializationContext::create()->setInitialType('array')],
            [[1 => 1, 2 => 2], "1: 1\n2: 2\n", SerializationContext::create()->setInitialType('array')],
            [[1 => 1, 2 => 2], "- 1\n- 2\n", SerializationContext::create()->setInitialType('array<integer>')],
            [['a', 'b'], "- a\n- b\n", SerializationContext::create()->setInitialType('array<string>')],

            [[1 => 'a', 2 => 'b'], "- a\n- b\n", SerializationContext::create()->setInitialType('array<string>')],
            [['a' => 'a', 'b' => 'b'], "- a\n- b\n", SerializationContext::create()->setInitialType('array<string>')],


            [[1, 2], "0: 1\n1: 2\n", SerializationContext::create()->setInitialType('array<integer,integer>')],
            [[1, 2], "0: 1\n1: 2\n", SerializationContext::create()->setInitialType('array<string,integer>')],
            [[1, 2], "0: 1\n1: 2\n", SerializationContext::create()->setInitialType('array<string,string>')],


            [['a', 'b'], "0: a\n1: b\n", SerializationContext::create()->setInitialType('array<integer,string>')],
            [['a' => 'a', 'b' => 'b'], "a: a\nb: b\n", SerializationContext::create()->setInitialType('array<string,string>')],
            [
                ['int' => 1, 'string' => 'a', 'dateTime' => new \DateTime('2018-07-07 10:10:10')],
                "int: 1\nstring: a\ndateTime: 2018-07-07 10:10:10\n",
                SerializationContext::create()->setInitialType('array<string,mixed>')
            ],
        ];
    }

    /**
     * @dataProvider getTypeHintedArrays
     * @param array $array
     * @param string $expected
     * @param SerializationContext|null $context
     */
    public function testTypeHintedArraySerialization(array $array, $expected, $context = null)
    {
        $this->assertEquals($expected, $this->serialize($array, $context));
    }


    protected function getFormat()
    {
        return 'yml';
    }

    protected function hasDeserializer()
    {
        return false;
    }
}
