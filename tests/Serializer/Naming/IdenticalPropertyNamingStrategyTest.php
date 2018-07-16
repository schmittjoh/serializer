<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\Naming;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use PHPUnit\Framework\TestCase;

class IdenticalPropertyNamingStrategyTest extends TestCase
{
    public function providePropertyNames()
    {
        return [
            ['createdAt'],
            ['my_field'],
            ['identical'],
        ];
    }

    /**
     * @dataProvider providePropertyNames
     */
    public function testTranslateName($propertyName)
    {
        $mockProperty = $this->getMockBuilder('JMS\Serializer\Metadata\PropertyMetadata')->disableOriginalConstructor()->getMock();
        $mockProperty->name = $propertyName;

        $strategy = new IdenticalPropertyNamingStrategy();
        self::assertEquals($propertyName, $strategy->translateName($mockProperty));
    }
}
