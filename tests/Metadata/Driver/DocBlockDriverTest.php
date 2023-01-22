<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\DocBlockDriver;
use JMS\Serializer\Metadata\Driver\TypedPropertiesDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionAsList;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesFromDifferentNamespace;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesFromDifferentNamespaceUsingGroupAlias;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesFromDifferentNamespaceUsingSingleAlias;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesFromGlobalNamespace;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesFromSameNamespace;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesFromTrait;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesFromTraitInsideTrait;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesWithFullNamespacePath;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesWithNull;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfClassesWithNullSingleLinePhpDoc;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfInterfacesFromDifferentNamespace;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfInterfacesFromGlobalNamespace;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfInterfacesFromSameNamespace;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfInterfacesWithFullNamespacePath;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfNotExistingClasses;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionOfScalars;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\CollectionTypedAsGenericClass;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\ConstructorPropertyPromotion;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\ConstructorPropertyPromotionWithoutDocblock;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\ConstructorPropertyPromotionWithScalar;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductColor;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductDescription;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Details\ProductName;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\MapTypedAsGenericClass;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Product;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Collection\Vehicle;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan\PhpstanArrayCollectionShape;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan\PhpstanArrayShape;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan\PhpstanMultipleArrayShapes;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan\PhpstanNestedArrayShape;
use JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan\ProductType;
use JMS\Serializer\Tests\Fixtures\DocBlockType\SingleClassFromDifferentNamespaceTypeHint;
use JMS\Serializer\Tests\Fixtures\DocBlockType\SingleClassFromGlobalNamespaceTypeHint;
use JMS\Serializer\Tests\Fixtures\DocBlockType\UnionTypedDocBLockProperty;
use PHPUnit\Framework\TestCase;

class DocBlockDriverTest extends TestCase
{
    private function resolve(string $classToResolve): ClassMetadata
    {
        if (PHP_VERSION_ID > 70400) {
            $baseDriver = new TypedPropertiesDriver(new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy()));
        } else {
            $baseDriver = new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy());
        }

        $driver = new DocBlockDriver($baseDriver);

        $m = $driver->loadMetadataForClass(new \ReflectionClass($classToResolve));
        self::assertNotNull($m);

        return $m;
    }

    public function testInferDocBlockCollectionOfScalars()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfScalars::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => 'string', 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockCollectionAsList(): void
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionAsList::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => 'int', 'params' => []], ['name' => 'string', 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromSameNamespace()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfClassesFromSameNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => Product::class, 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromUsingFullNamespacePath()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfClassesWithFullNamespacePath::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => Product::class, 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockCollectionFromGenericLikeClass()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionTypedAsGenericClass::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => Product::class, 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockMapFromGenericLikeClass()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(MapTypedAsGenericClass::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => 'int', 'params' => []], ['name' => Product::class, 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesIgnoringNullTypeHint()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfClassesWithNull::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => Product::class, 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesIgnoringNullTypeHintWithSingleLinePhpDoc()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfClassesWithNullSingleLinePhpDoc::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => Product::class, 'params' => []]]],
            $m->propertyMetadata['productIds']->type
        );
    }

    public function testThrowingExceptionWhenNotExistingClassWasGiven()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $this->expectException(\InvalidArgumentException::class);

        $this->resolve(CollectionOfNotExistingClasses::class);
    }

    public function testInferDocBlockCollectionOfClassesFromDifferentNamespace()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfClassesFromDifferentNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductDescription::class, 'params' => []]]],
            $m->propertyMetadata['productDescriptions']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromGlobalNamespace()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfClassesFromGlobalNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => \stdClass::class, 'params' => []]]],
            $m->propertyMetadata['products']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromDifferentNamespaceUsingSingleAlias()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfClassesFromDifferentNamespaceUsingSingleAlias::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductDescription::class, 'params' => []]]],
            $m->propertyMetadata['productDescriptions']->type
        );
    }

    public function testInferDocBlockCollectionOfClassesFromDifferentNamespaceUsingGroupAlias()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

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
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

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
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfClassesFromTraitInsideTrait::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductDescription::class, 'params' => []]]],
            $m->propertyMetadata['productDescriptions']->type
        );
    }

    public function testInferDocBlockCollectionOfInterfacesFromDifferentNamespace()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfInterfacesFromDifferentNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductColor::class, 'params' => []]]],
            $m->propertyMetadata['productColors']->type
        );
    }

    public function testInferDocBlockCollectionOfInterfacesFromGlobalNamespace()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfInterfacesFromGlobalNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductColor::class, 'params' => []]]],
            $m->propertyMetadata['productColors']->type
        );
    }

    public function testInferDocBlockCollectionOfInterfacesFromSameNamespace()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfInterfacesFromSameNamespace::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => Vehicle::class, 'params' => []]]],
            $m->propertyMetadata['vehicles']->type
        );
    }

    public function testInferDocBlockCollectionOfInterfacesWithFullNamespacePath()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped(sprintf('%s requires PHP 7.4', TypedPropertiesDriver::class));
        }

        $m = $this->resolve(CollectionOfInterfacesWithFullNamespacePath::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => ProductColor::class, 'params' => []]]],
            $m->propertyMetadata['productColors']->type
        );
    }

    public function testInferTypeForNonCollectionFromSameNamespaceType()
    {
        $m = $this->resolve(SingleClassFromGlobalNamespaceTypeHint::class);

        self::assertEquals(
            ['name' => \stdClass::class, 'params' => []],
            $m->propertyMetadata['data']->type
        );
    }

    public function testInferTypeForNonCollectionFromDifferentNamespaceType()
    {
        $m = $this->resolve(SingleClassFromDifferentNamespaceTypeHint::class);

        self::assertEquals(
            ['name' => ProductDescription::class, 'params' => []],
            $m->propertyMetadata['data']->type
        );
    }

    public function testInferTypeForNonUnionDocblockType()
    {
        $m = $this->resolve(UnionTypedDocBLockProperty::class);

        self::assertEquals(
            null,
            $m->propertyMetadata['data']->type
        );
    }

    public function testInferTypeForConstructorPropertyPromotion()
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Constructor property promotion requires PHP 8.0');
        }

        $m = $this->resolve(ConstructorPropertyPromotion::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => 'string', 'params' => []]]],
            $m->propertyMetadata['data']->type
        );
    }

    public function testInferTypeForConstructorPropertyPromotionWithoutDocblock()
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Constructor property promotion requires PHP 8.0');
        }

        $m = $this->resolve(ConstructorPropertyPromotionWithoutDocblock::class);

        self::assertEquals(
            null,
            $m->propertyMetadata['data']->type
        );
    }

    public function testInferTypeForConstructorPropertyPromotionWithScalar()
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Constructor property promotion requires PHP 8.0');
        }

        $m = $this->resolve(ConstructorPropertyPromotionWithScalar::class);

        self::assertEquals(
            ['name' => 'string', 'params' => []],
            $m->propertyMetadata['data']->type
        );
    }

    public function testInferTypeForPhpstanArray()
    {
        $m = $this->resolve(PhpstanArrayShape::class);

        self::assertEquals(
            ['name' => 'array', 'params' => []],
            $m->propertyMetadata['data']->type
        );
    }

    public function testInferTypeForPhpstanNestedArrayShape()
    {
        $m = $this->resolve(PhpstanNestedArrayShape::class);

        self::assertEquals(
            ['name' => 'array', 'params' => []],
            $m->propertyMetadata['data']->type
        );
    }

    public function testInferTypeForMultiplePhpstanArray()
    {
        $m = $this->resolve(PhpstanMultipleArrayShapes::class);

        self::assertEquals(
            ['name' => 'array', 'params' => []],
            $m->propertyMetadata['data']->type
        );
        self::assertEquals(
            ['name' => 'array', 'params' => []],
            $m->propertyMetadata['details']->type
        );
    }

    public function testInferTypeForPhpstanArrayCollection()
    {
        $m = $this->resolve(PhpstanArrayCollectionShape::class);

        self::assertEquals(
            ['name' => 'array', 'params' => [['name' => 'int', 'params' => []], ['name' => ProductType::class, 'params' => []]]],
            $m->propertyMetadata['data']->type
        );
    }
}
