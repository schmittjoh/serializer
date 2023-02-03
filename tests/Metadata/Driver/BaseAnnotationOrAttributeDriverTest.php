<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Tests\Fixtures\AllExcludedObject;
use Metadata\Driver\DriverInterface;

abstract class BaseAnnotationOrAttributeDriverTest extends BaseDriverTest
{
    abstract protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface;

    public function testAllExcluded(): void
    {
        $a = new AllExcludedObject();
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass($a));

        self::assertArrayNotHasKey('foo', $m->propertyMetadata);
        self::assertArrayHasKey('bar', $m->propertyMetadata);
    }

    public function testCanDefineMetadataForInternalClass(): void
    {
        $this->markTestSkipped('Can not define annotation or attribute metadata for internal classes');
    }

    public function testShortExposeSyntax(): void
    {
        $this->markTestSkipped('Short expose syntax not supported on annotations or attribute');
    }
}
