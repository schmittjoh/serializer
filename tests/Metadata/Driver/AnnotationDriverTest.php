<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\AllExcludedObject;

class AnnotationDriverTest extends BaseDriverTest
{
    public function testAllExcluded()
    {
        $a = new AllExcludedObject();
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass($a));

        self::assertArrayNotHasKey('foo', $m->propertyMetadata);
        self::assertArrayHasKey('bar', $m->propertyMetadata);
    }

    protected function getDriver()
    {
        return new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
    }

    public function testCanDefineMetadataForInternalClass()
    {
        $this->markTestSkipped('Can not define annotation metadata for internal classes');
    }
}
