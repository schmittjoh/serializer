<?php

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\NullDriver;

class NullDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsValidMetadata()
    {
        $driver = new NullDriver();

        $metadata = $driver->loadMetadataForClass(new \ReflectionClass('stdClass'));

        $this->assertInstanceOf(ClassMetadata::class, $metadata);
        $this->assertCount(0, $metadata->propertyMetadata);
    }
}
