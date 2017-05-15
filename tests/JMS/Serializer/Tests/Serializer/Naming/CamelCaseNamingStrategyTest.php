<?php


namespace JMS\Serializer\Tests\Serializer\Naming;

use JMS\Serializer\Naming\CamelCaseNamingStrategy;


class CamelCaseNamingStrategyTest extends \PHPUnit_Framework_TestCase {

    public function providePropertyNames() {
        return array(
            array('getUrl', 'get_url'),
            array('getURL', 'get_url')
        );
    }

    /**
     * @dataProvider providePropertyNames
     */
    public function testCamelCaseNamingHandlesMultipleUppercaseLetters($propertyName, $expected) {
        $mockProperty = $this->getMockBuilder('JMS\Serializer\Metadata\PropertyMetadata')->disableOriginalConstructor()->getMock();
        $mockProperty->name = $propertyName;

        $strategy = new CamelCaseNamingStrategy();
        $this->assertEquals($expected, $strategy->translateName($mockProperty));
    }

} 