<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\Driver\FileLocator;

class YamlDriverTest extends BaseDriverTest
{
    public function testAccessorOrderIsInferred()
    {
        $m = $this->getDriverForSubDir('accessor_inferred')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Person'));
        self::assertEquals(['age', 'name'], array_keys($m->propertyMetadata));
    }

    public function testShortExposeSyntax()
    {
        $m = $this->getDriverForSubDir('short_expose')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Person'));

        self::assertArrayHasKey('name', $m->propertyMetadata);
        self::assertArrayNotHasKey('age', $m->propertyMetadata);
    }

    public function testBlogPost()
    {
        $m = $this->getDriverForSubDir('exclude_all')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        self::assertArrayHasKey('title', $m->propertyMetadata);

        $excluded = ['createdAt', 'published', 'comments', 'author'];
        foreach ($excluded as $key) {
            self::assertArrayNotHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostExcludeNoneStrategy()
    {
        $m = $this->getDriverForSubDir('exclude_none')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        self::assertArrayNotHasKey('title', $m->propertyMetadata);

        $excluded = ['createdAt', 'published', 'comments', 'author'];
        foreach ($excluded as $key) {
            self::assertArrayHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostCaseInsensitive()
    {
        $m = $this->getDriverForSubDir('case')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        $p = new PropertyMetadata($m->name, 'title');
        $p->serializedName = 'title';
        $p->type = ['name' => 'string', 'params' => []];
        self::assertEquals($p, $m->propertyMetadata['title']);
    }

    public function testBlogPostAccessor()
    {
        $m = $this->getDriverForSubDir('accessor')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        self::assertArrayHasKey('title', $m->propertyMetadata);

        $p = new PropertyMetadata($m->name, 'title');
        $p->getter = 'getOtherTitle';
        $p->setter = 'setOtherTitle';
        $p->serializedName = 'title';
        self::assertEquals($p, $m->propertyMetadata['title']);
    }

    private function getDriverForSubDir($subDir = null)
    {
        return new YamlDriver(new FileLocator([
            'JMS\Serializer\Tests\Fixtures' => __DIR__ . '/yml' . ($subDir ? '/' . $subDir : ''),
            '' => __DIR__ . '/yml/_' . ($subDir ? '/' . $subDir : ''),
        ]), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
    }

    protected function getDriver()
    {
        return $this->getDriverForSubDir();
    }
}
