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

        $this->stub->setStringAsLowerCase($expected, 'propertyOne');
        $this->assertEquals(strtolower($expected), $this->stub->getPropertyOne());
        $this->assertEquals(strtoupper($expected), $this->stub->getStringAsUpperCase('propertyOne'));
        $this->assertEmpty($this->stub->getStringAsUpperCase('invalidProperty'));
    }

    public function testSerialization()
    {
        $expected = 'Test Data';

        $stdObject = new \ArrayObject(array('property_one' =>  $expected, 'property_two' => $expected));

        /** @var GenericStringManipulation $testStub */
        $testStub =  $this->serializer->deserialize(json_encode($stdObject),'JMS\Serializer\Tests\Fixtures\GenericStringManipulation', 'json');
        $this->assertEquals(strtoupper($expected),json_decode($this->serializer->serialize($testStub, 'json'))->property_one);
        $this->assertEquals(strtoupper($expected),json_decode($this->serializer->serialize($testStub, 'json'))->property_two);
        $this->assertEquals(strtolower($expected),$testStub->getPropertyOne());
        $this->assertEquals(strtolower($expected),$testStub->getPropertyTwo());
    }


}
