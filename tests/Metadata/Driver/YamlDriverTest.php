<?php

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\Driver\FileLocator;

class YamlDriverTest extends BaseDriverTest
{
    public function testAccessorOrderIsInferred()
    {
        $m = $this->getDriverForSubDir('accessor_inferred')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Person'));
        $this->assertEquals(array('age', 'name'), array_keys($m->propertyMetadata));
    }

    public function testShortExposeSyntax()
    {
        $m = $this->getDriverForSubDir('short_expose')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Person'));

        $this->assertArrayHasKey('name', $m->propertyMetadata);
        $this->assertArrayNotHasKey('age', $m->propertyMetadata);
    }

    public function testBlogPost()
    {
        $m = $this->getDriverForSubDir('exclude_all')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        $this->assertArrayHasKey('title', $m->propertyMetadata);

        $excluded = array('createdAt', 'published', 'comments', 'author');
        foreach ($excluded as $key) {
            $this->assertArrayNotHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostExcludeNoneStrategy()
    {
        $m = $this->getDriverForSubDir('exclude_none')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        $this->assertArrayNotHasKey('title', $m->propertyMetadata);

        $excluded = array('createdAt', 'published', 'comments', 'author');
        foreach ($excluded as $key) {
            $this->assertArrayHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostCaseInsensitive()
    {
        $m = $this->getDriverForSubDir('case')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        $p = new PropertyMetadata($m->name, 'title');
        $p->type = array('name' => 'string', 'params' => array());
        $this->assertEquals($p, $m->propertyMetadata['title']);
    }

    public function testBlogPostAccessor()
    {
        $m = $this->getDriverForSubDir('accessor')->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        $this->assertArrayHasKey('title', $m->propertyMetadata);

        $p = new PropertyMetadata($m->name, 'title');
        $p->getter = 'getOtherTitle';
        $p->setter = 'setOtherTitle';
        $this->assertEquals($p, $m->propertyMetadata['title']);
    }

    private function getDriverForSubDir($subDir = null)
    {
        return new YamlDriver(new FileLocator(array(
            'JMS\Serializer\Tests\Fixtures' => __DIR__ . '/yml' . ($subDir ? '/' . $subDir : ''),
        )));
    }

    protected function getDriver()
    {
        return $this->getDriverForSubDir();
    }
}
