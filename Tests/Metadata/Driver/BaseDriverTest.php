<?php

namespace JMS\SerializerBundle\Tests\Metadata\Driver;

use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\ClassMetadata;

abstract class BaseDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadBlogPostMetadata()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\BlogPost'));

        $this->assertNotNull($m);
        $this->assertEquals('blog-post', $m->xmlRootName);

        $p = new PropertyMetadata($m->name, 'title');
        $p->type = 'string';
        $this->assertEquals($p, $m->propertyMetadata['title']);

        $p = new PropertyMetadata($m->name, 'createdAt');
        $p->type = 'DateTime';
        $p->xmlAttribute = true;
        $this->assertEquals($p, $m->propertyMetadata['createdAt']);

        $p = new PropertyMetadata($m->name, 'published');
        $p->type = 'boolean';
        $p->serializedName = 'is_published';
        $p->xmlAttribute = true;
        $this->assertEquals($p, $m->propertyMetadata['published']);

        $p = new PropertyMetadata($m->name, 'comments');
        $p->type = 'ArrayCollection<JMS\SerializerBundle\Tests\Fixtures\Comment>';
        $p->xmlCollection = true;
        $p->xmlCollectionInline = true;
        $p->xmlEntryName = 'comment';
        $this->assertEquals($p, $m->propertyMetadata['comments']);

        $p = new PropertyMetadata($m->name, 'author');
        $p->type = 'JMS\SerializerBundle\Tests\Fixtures\Author';
        $this->assertEquals($p, $m->propertyMetadata['author']);
        
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\Price'));
        $this->assertNotNull($m);
        
        $p = new PropertyMetadata($m->name, 'price');
        $p->type = 'double';
        $p->xmlValue = true;
        $this->assertEquals($p, $m->propertyMetadata['price']);
    }

    abstract protected function getDriver();
}