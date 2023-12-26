<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\NullDriver;
use JMS\Serializer\Metadata\Driver\TypedPropertiesDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\TypedProperties\UnionTypedProperties;
use Metadata\Driver\DriverChain;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class UnionTypedPropertiesDriverTest extends TestCase
{
    protected function setUp(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped(sprintf('%s requires PHP 8.0', TypedPropertiesDriver::class));
        }
    }

    public function testInferUnionTypesShouldResultInNoType()
    {
        $m = $this->resolve(UnionTypedProperties::class);

        self::assertEquals(
            null,
            $m->propertyMetadata['data']->type,
        );
    }

    private function resolve(string $classToResolve): ClassMetadata
    {
        $namingStrategy = new IdenticalPropertyNamingStrategy();

        $driver = new DriverChain([
            new AnnotationDriver(new AnnotationReader(), $namingStrategy),
            new NullDriver($namingStrategy),
        ]);

        $driver = new TypedPropertiesDriver($driver);

        $m = $driver->loadMetadataForClass(new ReflectionClass($classToResolve));
        self::assertNotNull($m);

        return $m;
    }
}
