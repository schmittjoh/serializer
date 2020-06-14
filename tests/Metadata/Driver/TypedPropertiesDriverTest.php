<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use InvalidArgumentException;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\DocBlockTypeResolver;
use JMS\Serializer\Metadata\Driver\TypedPropertiesDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfClassesFromDifferentNamespace;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfClassesFromDifferentNamespaceUsingGroupAlias;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfClassesFromDifferentNamespaceUsingSingleAlias;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfClassesFromGlobalNamespace;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfClassesFromSameNamespace;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfClassesFromTrait;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfClassesFromTraitInsideTrait;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfClassesWithNull;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfNotExistingClasses;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfScalars;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\CollectionOfUnionClasses;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductDescription;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Details\ProductName;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\IncorrectCollection;
use JMS\Serializer\Tests\Fixtures\TypedProperties\Collection\Product;
use JMS\Serializer\Tests\Fixtures\TypedProperties\User;
use Metadata\ClassMetadata;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

class TypedPropertiesDriverTest extends TestCase
{
    public function testInferPropertiesFromTypes()
    {
        $m = $this->resolve(User::class);

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

    private function resolve(string $classToResolve): ClassMetadata
    {
        $baseDriver = new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy());
        $driver = new TypedPropertiesDriver($baseDriver, new DocBlockTypeResolver());

        $m = $driver->loadMetadataForClass(new ReflectionClass($classToResolve));
        self::assertNotNull($m);

        return $m;
    }

    public function testInferDocBlockCollectionOfScalars()
    {
        $m = $this->resolve(CollectionOfScalars::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => 'string', 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromSameNamespace()
    {
        $m = $this->resolve(CollectionOfClassesFromSameNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => Product::class, 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesIgnoringNullTypeHint()
    {
        $m = $this->resolve(CollectionOfClassesWithNull::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => Product::class, 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testThrowingExceptionWhenUnionTypeIsUsedForCollection()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->resolve(CollectionOfUnionClasses::class);
    }

    public function testThrowingExceptionWhenIncorrectCollectionGiven()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->resolve(IncorrectCollection::class);
    }

    public function testThrowingExceptionWhenNotExistingClassWasGiven()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->resolve(CollectionOfNotExistingClasses::class);
    }

    public function testInferDocBlockCollectionOfClassesFromDifferentNamespace()
    {
        $m = $this->resolve(CollectionOfClassesFromDifferentNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductDescription::class, 'params' => []]]],
            $m->propertyMetadata['productDescriptions']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromGlobalNamespace()
    {
        $m = $this->resolve(CollectionOfClassesFromGlobalNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => stdClass::class, 'params' => []]]],
            $m->propertyMetadata['products']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromDifferentNamespaceUsingSingleAlias()
    {
        $m = $this->resolve(CollectionOfClassesFromDifferentNamespaceUsingSingleAlias::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductDescription::class, 'params' => []]]],
            $m->propertyMetadata['productDescriptions']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromDifferentNamespaceUsingGroupAlias()
    {
        $m = $this->resolve(CollectionOfClassesFromDifferentNamespaceUsingGroupAlias::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductDescription::class, 'params' => []]]],
            $m->propertyMetadata['productDescriptions']->type
        );
        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductName::class, 'params' => []]]],
            $m->propertyMetadata['productNames']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromTraits()
    {
        $m = $this->resolve(CollectionOfClassesFromTrait::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductDescription::class, 'params' => []]]],
            $m->propertyMetadata['productDescriptions']->type
        );
        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductName::class, 'params' => []]]],
            $m->propertyMetadata['productNames']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromTraitInsideTrait()
    {
        $m = $this->resolve(CollectionOfClassesFromTraitInsideTrait::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductDescription::class, 'params' => []]]],
            $m->propertyMetadata['productDescriptions']->type
        );
    }

    protected function setUp(): void
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }
    }
}
