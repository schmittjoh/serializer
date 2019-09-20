<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\BlogPost;
use JMS\Serializer\Tests\Fixtures\Person;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;

class YamlDriverTest extends BaseDriverTest
{
    public function testAccessorOrderIsInferred(): void
    {
        $m = $this->getDriver('accessor_inferred')->loadMetadataForClass(new \ReflectionClass(Person::class));
        self::assertEquals(['age', 'name'], array_keys($m->propertyMetadata));
    }

    public function testShortExposeSyntax(): void
    {
        $m = $this->getDriver('short_expose')->loadMetadataForClass(new \ReflectionClass(Person::class));

        self::assertArrayHasKey('name', $m->propertyMetadata);
        self::assertArrayNotHasKey('age', $m->propertyMetadata);
    }

    public function testBlogPost(): void
    {
        $m = $this->getDriver('exclude_all')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));

        self::assertArrayHasKey('title', $m->propertyMetadata);

        $excluded = ['createdAt', 'published', 'comments', 'author'];
        foreach ($excluded as $key) {
            self::assertArrayNotHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostExcludeNoneStrategy(): void
    {
        $m = $this->getDriver('exclude_none')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));

        self::assertArrayNotHasKey('title', $m->propertyMetadata);

        $excluded = ['createdAt', 'published', 'comments', 'author'];
        foreach ($excluded as $key) {
            self::assertArrayHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostCaseInsensitive(): void
    {
        $m = $this->getDriver('case')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));

        $p = new PropertyMetadata($m->name, 'title');
        $p->serializedName = 'title';
        $p->type = ['name' => 'string', 'params' => []];
        self::assertEquals($p, $m->propertyMetadata['title']);
    }

    public function testBlogPostAccessor(): void
    {
        $m = $this->getDriver('accessor')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));

        self::assertArrayHasKey('title', $m->propertyMetadata);

        $p = new PropertyMetadata($m->name, 'title');
        $p->getter = 'getOtherTitle';
        $p->setter = 'setOtherTitle';
        $p->serializedName = 'title';
        self::assertEquals($p, $m->propertyMetadata['title']);
    }

    public function testInvalidMetadataFileCausesException(): void
    {
        $this->expectException(InvalidMetadataException::class);

        $this->getDriver('invalid_metadata')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));
    }

    public function testLoadingYamlFileWithLongExtension(): void
    {
        $m = $this->getDriver('multiple_types')->loadMetadataForClass(new \ReflectionClass(Person::class));

        self::assertArrayHasKey('name', $m->propertyMetadata);
    }

    public function testLoadingMultipleMetadataExtensions(): void
    {
        $classNames = $this->getDriver('multiple_types', false)->getAllClassNames();

        self::assertEquals(
            [
                BlogPost::class,
                Person::class,
            ],
            $classNames
        );
    }

    protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface
    {
        $dirs = [
            'JMS\Serializer\Tests\Fixtures' => __DIR__ . '/yml' . ($subDir ? '/' . $subDir : ''),
        ];

        if ($addUnderscoreDir) {
            $dirs[''] = __DIR__ . '/yml/_' . ($subDir ? '/' . $subDir : '');
        }

        return new YamlDriver(new FileLocator($dirs), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
    }
}
