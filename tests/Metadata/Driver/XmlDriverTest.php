<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Metadata\Driver\XmlDriver;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;

class XmlDriverTest extends BaseDriverTestCase
{
    public function testInvalidXml()
    {
        $driver = $this->getDriver();

        $ref = new \ReflectionMethod($driver, 'loadMetadataFromFile');
        $ref->setAccessible(true);

        $this->expectException(InvalidMetadataException::class);
        $this->expectExceptionMessage('Invalid XML content for metadata');

        $ref->invoke($driver, new \ReflectionClass('stdClass'), __DIR__ . '/xml/invalid.xml');
    }

    public function testBlogPostExcludeAllStrategy()
    {
        $m = $this->getDriver('exclude_all')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        self::assertArrayHasKey('title', $m->propertyMetadata);

        $excluded = ['createdAt', 'published', 'comments', 'author'];
        foreach ($excluded as $key) {
            self::assertArrayNotHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostExcludeNoneStrategy()
    {
        $m = $this->getDriver('exclude_none')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        self::assertArrayNotHasKey('title', $m->propertyMetadata);

        $excluded = ['createdAt', 'published', 'comments', 'author'];
        foreach ($excluded as $key) {
            self::assertArrayHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostCaseInsensitive()
    {
        $m = $this->getDriver('case')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        $p = new PropertyMetadata($m->name, 'title');
        $p->serializedName = 'title';
        $p->type = ['name' => 'string', 'params' => []];
        self::assertEquals($p, $m->propertyMetadata['title']);
    }

    public function testAccessorAttributes()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\GetSetObject'));

        $p = new PropertyMetadata($m->name, 'name');
        $p->type = ['name' => 'string', 'params' => []];
        $p->getter = 'getTrimmedName';
        $p->setter = 'setCapitalizedName';
        $p->serializedName = 'name';

        self::assertEquals($p, $m->propertyMetadata['name']);
    }

    public function testGroupsTrim()
    {
        $first = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\GroupsTrim'));

        self::assertArrayHasKey('amount', $first->propertyMetadata);
        self::assertContains('first.test.group', $first->propertyMetadata['currency']->groups);
        self::assertContains('second.test.group', $first->propertyMetadata['currency']->groups);
    }

    public function testMultilineGroups()
    {
        $first = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\MultilineGroupsFormat'));

        self::assertArrayHasKey('amount', $first->propertyMetadata);
        self::assertContains('first.test.group', $first->propertyMetadata['currency']->groups);
        self::assertContains('second.test.group', $first->propertyMetadata['currency']->groups);
    }

    /**
     * @return XmlDriver
     */
    protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface
    {
        $dirs = [
            'JMS\Serializer\Tests\Fixtures' => __DIR__ . '/xml' . ($subDir ? '/' . $subDir : ''),
        ];

        if ($addUnderscoreDir) {
            $dirs[''] = __DIR__ . '/xml/_' . ($subDir ? '/' . $subDir : '');
        }

        return new XmlDriver(new FileLocator($dirs), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
    }
}
