<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\TypedPropertiesDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\TypedProperties\UnionTypedProperties;
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

    public function testInferUnionTypesShouldResultInManyTypes()
    {
        $m = $this->resolve(UnionTypedProperties::class);

        self::assertEquals(
            [
                'name' => 'union',
                'params' => [
                    [
                        'name' => 'string',
                        'params' => [],
                    ],
                    [
                        'name' => 'int',
                        'params' => [],
                    ],
                    [
                        'name' => 'float',
                        'params' => [],
                    ],
                    [
                        'name' => 'bool',
                        'params' => [],
                    ],
                ],
            ],
            $m->propertyMetadata['data']->type
        );
    }

    private function resolve(string $classToResolve): ClassMetadata
    {
        $baseDriver = new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy());
        $driver = new TypedPropertiesDriver($baseDriver);

        $m = $driver->loadMetadataForClass(new ReflectionClass($classToResolve));
        self::assertNotNull($m);

        return $m;
    }
}
