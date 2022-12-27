<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\TypedProperties\User;
use PHPUnit\Framework\TestCase;

class DefaultDriverFactoryTest extends TestCase
{
    public function testDefaultDriverFactoryLoadsTypedPropertiesDriver()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', __METHOD__));
        }

        $factory = new DefaultDriverFactory(new IdenticalPropertyNamingStrategy());

        $driver = $factory->createDriver([], new AnnotationReader());

        $m = $driver->loadMetadataForClass(new \ReflectionClass(User::class));
        \assert($m instanceof ClassMetadata);
        self::assertNotNull($m);

        $expectedPropertyTypes = [
            'id' => 'int',
            'role' => 'JMS\Serializer\Tests\Fixtures\TypedProperties\Role',
            'created' => 'DateTime',
            'tags' => 'iterable',
        ];

        foreach ($expectedPropertyTypes as $property => $type) {
            self::assertEquals(['name' => $type, 'params' => []], $m->propertyMetadata[$property]->type);
        }
    }
}
