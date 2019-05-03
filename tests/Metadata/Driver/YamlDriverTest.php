<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\BlogPost;
use JMS\Serializer\Tests\Fixtures\Person;
use Metadata\Driver\FileLocator;

class YamlDriverTest extends BaseDriverTest
{
    public function testAccessorOrderIsInferred(): void
    {
        $m = $this->getDriverForSubDir('accessor_inferred')->loadMetadataForClass(new \ReflectionClass(Person::class));
        self::assertEquals(['age', 'name'], array_keys($m->propertyMetadata));
    }

    public function testShortExposeSyntax(): void
    {
        $m = $this->getDriverForSubDir('short_expose')->loadMetadataForClass(new \ReflectionClass(Person::class));

        self::assertArrayHasKey('name', $m->propertyMetadata);
        self::assertArrayNotHasKey('age', $m->propertyMetadata);
    }

    public function testBlogPost(): void
    {
        $m = $this->getDriverForSubDir('exclude_all')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));

        self::assertArrayHasKey('title', $m->propertyMetadata);

        $excluded = ['createdAt', 'published', 'comments', 'author'];
        foreach ($excluded as $key) {
            self::assertArrayNotHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostExcludeNoneStrategy(): void
    {
        $m = $this->getDriverForSubDir('exclude_none')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));

        self::assertArrayNotHasKey('title', $m->propertyMetadata);

        $excluded = ['createdAt', 'published', 'comments', 'author'];
        foreach ($excluded as $key) {
            self::assertArrayHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostCaseInsensitive(): void
    {
        $m = $this->getDriverForSubDir('case')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));

        $p = new PropertyMetadata($m->name, 'title');
        $p->serializedName = 'title';
        $p->type = ['name' => 'string', 'params' => []];
        self::assertEquals($p, $m->propertyMetadata['title']);
    }

    public function testBlogPostAccessor(): void
    {
        $m = $this->getDriverForSubDir('accessor')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));

        self::assertArrayHasKey('title', $m->propertyMetadata);

        $p = new PropertyMetadata($m->name, 'title');
        $p->getter = 'getOtherTitle';
        $p->setter = 'setOtherTitle';
        $p->serializedName = 'title';
        self::assertEquals($p, $m->propertyMetadata['title']);
    }

    /**
     * @expectedException  \JMS\Serializer\Exception\InvalidMetadataException
     */
    public function testInvalidMetadataFileCausesException(): void
    {
        $this->getDriverForSubDir('invalid_metadata')->loadMetadataForClass(new \ReflectionClass(BlogPost::class));
    }

    public function testLoadingYamlFileWithLongExtension(): void
    {
        $m = $this->getDriverForSubDir('multiple_types')->loadMetadataForClass(new \ReflectionClass(Person::class));

        self::assertArrayHasKey('name', $m->propertyMetadata);
    }

    public function testLoadingMultipleMetadataExtensions(): void
    {
        $classNames = $this->getDriverForSubDir('multiple_types', false)->getAllClassNames();

        self::assertEquals(
            [
                BlogPost::class,
                Person::class,
            ],
            $classNames
        );
    }

    private function getDriverForSubDir($subDir = null, bool $addUnderscoreDir = true): YamlDriver
    {
        $dirs = [
            'JMS\Serializer\Tests\Fixtures' => __DIR__ . '/yml' . ($subDir ? '/' . $subDir : ''),
        ];

        if ($addUnderscoreDir) {
            $dirs[''] = __DIR__ . '/yml/_' . ($subDir ? '/' . $subDir : '');
        }

        return new YamlDriver(new FileLocator($dirs), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
    }

    protected function getDriver()
    {
        return $this->getDriverForSubDir();
    }
}
