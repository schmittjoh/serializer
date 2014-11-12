<?php

namespace JMS\Serializer\Tests\Annotation;

use JMS\Serializer\Tests\Fixtures\GenericStringManipulation;

class testStub
{
    use GenericStringManipulation;
}

class GenericStringTest extends \PHPUnit_Framework_TestCase
{
    /** @var  testStub */
    protected $stub;

    public function setUp()
    {
        $this->stub = new testStub();
    }

    public function testSetUp()
    {
        $this->assertTrue($this->stub instanceof testStub);
    }

    public function testAccessors()
    {
        $expected = 'Test Data';

        $this->stub->setStringAsLowerCase($expected, 'testString');
        $this->assertEquals(strtolower($expected), $this->stub->getTestString());
        $this->assertEquals(strtoupper($expected), $this->stub->getStringAsUpperCase('testString'));
        $this->assertEmpty($this->stub->getStringAsUpperCase('invalidProperty'));

    }


}
