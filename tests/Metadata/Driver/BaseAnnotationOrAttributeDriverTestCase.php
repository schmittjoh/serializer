<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Tests\Fixtures\AllExcludedObject;
use JMS\Serializer\Tests\Fixtures\MissingAttributeObject;
use ReflectionClass;

abstract class BaseAnnotationOrAttributeDriverTestCase extends BaseDriverTestCase
{
    public function testAllExcluded(): void
    {
        $a = new AllExcludedObject();
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass($a));

        self::assertArrayNotHasKey('foo', $m->propertyMetadata);
        self::assertArrayHasKey('bar', $m->propertyMetadata);
    }

    public function testCanDefineMetadataForInternalClass(): void
    {
        $this->markTestSkipped('Cannot define annotation or attribute metadata for internal classes');
    }

    public function testShortExposeSyntax(): void
    {
        $this->markTestSkipped('Short expose syntax not supported on annotations or attribute');
    }

    public function testCanHandleMissingAttributes(): void
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new ReflectionClass(MissingAttributeObject::class));
        self::assertArrayHasKey('property', $metadata->propertyMetadata);

        self::assertArrayHasKey('propertyFromMethod', $metadata->propertyMetadata);
    }
}
