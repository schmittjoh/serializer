<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\NullDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use PHPUnit\Framework\TestCase;

class NullDriverTest extends TestCase
{
    public function testReturnsValidMetadata()
    {
        $driver = new NullDriver(new IdenticalPropertyNamingStrategy());

        $metadata = $driver->loadMetadataForClass(new \ReflectionClass('stdClass'));

        self::assertInstanceOf(ClassMetadata::class, $metadata);
        self::assertCount(0, $metadata->propertyMetadata);
    }
}
