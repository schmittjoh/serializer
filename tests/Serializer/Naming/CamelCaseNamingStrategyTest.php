<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\Naming;

use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use PHPUnit\Framework\TestCase;

class CamelCaseNamingStrategyTest extends TestCase
{
    public function providePropertyNames()
    {
        return [
            ['getUrl', 'get_url'],
            ['getURL', 'get_url'],
        ];
    }

    /**
     * @dataProvider providePropertyNames
     */
    public function testCamelCaseNamingHandlesMultipleUppercaseLetters($propertyName, $expected)
    {
        $mockProperty = $this->getMockBuilder('JMS\Serializer\Metadata\PropertyMetadata')->disableOriginalConstructor()->getMock();
        $mockProperty->name = $propertyName;

        $strategy = new CamelCaseNamingStrategy();
        self::assertEquals($expected, $strategy->translateName($mockProperty));
    }
}
