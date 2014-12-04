<?php

namespace BDBStudios\Serializer\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use BDBStudios\Serializer\Builder\DefaultDriverFactory;
use BDBStudios\Serializer\Metadata\Driver\AnnotationDriver;
use BDBStudios\Serializer\Naming\CamelCaseNamingStrategy;
use BDBStudios\Serializer\Naming\SerializedNameAnnotationStrategy;
use BDBStudios\Serializer\Serializer;
use BDBStudios\Serializer\SerializerBuilder;
use BDBStudios\Serializer\Annotation\Type;
use BDBStudios\Serializer\Annotation\GenericAccessor;
use BDBStudios\Serializer\Tests\Fixtures\GenericStringManipulation;


class GenericStringTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    protected $dispatcher;

    /** @var Serializer */
    protected $serializer;

    protected $handlerRegistry;

    protected $serializationVisitors;

    protected $deserializationVisitors;

    /** @var  GenericStringManipulation */
    protected $stub;

    public function setUp()
    {
        $this->stub = new GenericStringManipulation();

        $this->factory = new DefaultDriverFactory(new AnnotationDriver(new AnnotationReader()));

        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());

        $this->serializer = SerializerBuilder::create();
        $this->serializer->setPropertyNamingStrategy($namingStrategy);
        $this->serializer->setMetadataDriverFactory($this->factory);
        $this->serializer = $this->serializer->build();
    }

    public function testSetUp()
    {
        $this->assertTrue($this->stub instanceof GenericStringManipulation);
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
        $testStub =  $this->serializer->deserialize(json_encode($stdObject),'BDBStudios\Serializer\Tests\Fixtures\GenericStringManipulation', 'json');
        $this->assertEquals(strtoupper($expected),json_decode($this->serializer->serialize($testStub, 'json'))->property_one);
        $this->assertEquals(strtoupper($expected),json_decode($this->serializer->serialize($testStub, 'json'))->property_two);
        $this->assertEquals(strtolower($expected),$testStub->getPropertyOne());
        $this->assertEquals(strtolower($expected),$testStub->getPropertyTwo());
    }


}
