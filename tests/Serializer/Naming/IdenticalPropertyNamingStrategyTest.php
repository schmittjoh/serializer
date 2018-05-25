<?php

namespace JMS\Serializer\Tests\Serializer\Naming;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;

class IdenticalPropertyNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function providePropertyNames()
    {
        return array(
            array('createdAt'),
            array('my_field'),
            array('identical')
        );
    }

    /**
     * @dataProvider providePropertyNames
     */
    public function testTranslateName($propertyName)
    {
        $mockProperty = $this->getMockBuilder('JMS\Serializer\Metadata\PropertyMetadata')->disableOriginalConstructor()->getMock();
        $mockProperty->name = $propertyName;

        $strategy = new IdenticalPropertyNamingStrategy();
        $this->assertEquals($propertyName, $strategy->translateName($mockProperty));
    }
}
