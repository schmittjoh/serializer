<?php

namespace JMS\Serializer\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\GenericStringManipulation;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\GenericAccessor;

class testStub
{
    use GenericStringManipulation;
}

class GenericStringTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    protected $dispatcher;

    /** @var Serializer */
    protected $serializer;

    protected $handlerRegistry;

    protected $serializationVisitors;

    protected $deserializationVisitors;

    /** @var  testStub */
    protected $stub;

    public function setUp()
    {
        $this->stub = new testStub();

        $this->factory = new DefaultDriverFactory(new AnnotationDriver(new AnnotationReader()));

        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());

        $this->serializer = SerializerBuilder::create();
        $this->serializer->setPropertyNamingStrategy($namingStrategy);
        $this->serializer->setMetadataDriverFactory($this->factory);
        $this->serializer = $this->serializer->build();
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

        /** @var testStub $testStub */
        $testStub =  $this->serializer->deserialize(json_encode($stdObject),'JMS\Serializer\Tests\Annotation\testStub', 'json');
        $this->assertEquals(strtoupper($expected),json_decode($this->serializer->serialize($testStub, 'json'))->property_one);
        $this->assertEquals(strtoupper($expected),json_decode($this->serializer->serialize($testStub, 'json'))->property_two);
        $this->assertEquals(strtolower($expected),$testStub->getPropertyOne());
        $this->assertEquals(strtolower($expected),$testStub->getPropertyTwo());
    }


}
