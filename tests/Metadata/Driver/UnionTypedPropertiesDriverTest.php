<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\NullDriver;
use JMS\Serializer\Metadata\Driver\TypedPropertiesDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\TypedProperties\NotSupportedDNFTypes;
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

    public function testInferUnionTypesShouldResultInManyTypes()
    {
        $m = $this->resolve(UnionTypedProperties::class);

        self::assertEquals(
            [
                'name' => 'union',
                'params' =>
                    [
                        [
                            [
                                'name' => 'array',
                                'params' => [],
                            ],
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
            ],
            $m->propertyMetadata['data']->type,
        );
    }

    public function testInferUnionTypesShouldIncludeValueTypes()
    {
        $m = $this->resolve(UnionTypedProperties::class);

        self::assertEquals(
            [
                'name' => 'union',
                'params' =>
                    [
                        [
                            [
                                'name' => 'string',
                                'params' => [],
                            ],
                            [
                                'name' => 'false',
                                'params' => [],
                            ],
                        ],
                    ],
            ],
            $m->propertyMetadata['valueTypedUnion']->type,
        );
    }

    public function testDNFTypes()
    {
        if (PHP_VERSION_ID < 80200) {
            self::markTestSkipped();
        }

        $m = $this->resolve(NotSupportedDNFTypes::class);

        self::assertCount(10, $m->propertyMetadata);
        foreach ($m->propertyMetadata as $propertyMetadata) {
            self::assertNull($propertyMetadata->type, 'Breaking Change: TypeResolved for: ' . $propertyMetadata->name);
        }
    }

    private function resolve(string $classToResolve): ClassMetadata
    {
        $namingStrategy = new IdenticalPropertyNamingStrategy();

        $driver = new DriverChain([
            new AnnotationDriver(new AnnotationReader(), $namingStrategy),
            new NullDriver($namingStrategy),
        ]);

        $driver = new TypedPropertiesDriver($driver, null, [], true);

        $m = $driver->loadMetadataForClass(new ReflectionClass($classToResolve));
        self::assertNotNull($m);

        return $m;
    }
}
