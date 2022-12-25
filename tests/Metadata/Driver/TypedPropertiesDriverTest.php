<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\TypedPropertiesDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\TypedProperties\User;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TypedPropertiesDriverTest extends TestCase
{
    protected function setUp(): void
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }
    }

    public function testInferPropertiesFromTypes()
    {
        $m = $this->resolve(User::class);

        $expectedPropertyTypes = [
            'id' => 'int',
            'role' => 'JMS\Serializer\Tests\Fixtures\TypedProperties\Role',
            'vehicle' => 'JMS\Serializer\Tests\Fixtures\TypedProperties\Vehicle',
            'created' => 'DateTime',
            'tags' => 'iterable',
        ];

        foreach ($expectedPropertyTypes as $property => $type) {
            self::assertEquals(['name' => $type, 'params' => []], $m->propertyMetadata[$property]->type);
        }
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
