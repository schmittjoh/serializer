<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\AllExcludedObject;
use Metadata\Driver\DriverInterface;

class AnnotationDriverTest extends BaseDriverTest
{
    public function testAllExcluded()
    {
        $a = new AllExcludedObject();
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass($a));

        self::assertArrayNotHasKey('foo', $m->propertyMetadata);
        self::assertArrayHasKey('bar', $m->propertyMetadata);
    }

    protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface
    {
        return new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
    }

    public function testCanDefineMetadataForInternalClass()
    {
        $this->markTestSkipped('Can not define annotation metadata for internal classes');
    }

    public function testShortExposeSyntax(): void
    {
        $this->markTestSkipped('Short expose syntax not supported on annotations');
    }
}
