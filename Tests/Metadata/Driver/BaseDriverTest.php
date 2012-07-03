<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Tests\Metadata\Driver;

use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\VirtualPropertyMetadata;

abstract class BaseDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadBlogPostMetadata()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\BlogPost'));

        $this->assertNotNull($m);
        $this->assertEquals('blog-post', $m->xmlRootName);

        $p = new PropertyMetadata($m->name, 'title');
        $p->type = 'string';
        $p->groups = array("comments","post");
        $this->assertEquals($p, $m->propertyMetadata['title']);

        $p = new PropertyMetadata($m->name, 'createdAt');
        $p->type = 'DateTime';
        $p->xmlAttribute = true;
        $this->assertEquals($p, $m->propertyMetadata['createdAt']);

        $p = new PropertyMetadata($m->name, 'published');
        $p->type = 'boolean';
        $p->serializedName = 'is_published';
        $p->xmlAttribute = true;
        $p->groups = array("post");
        $this->assertEquals($p, $m->propertyMetadata['published']);

        $p = new PropertyMetadata($m->name, 'comments');
        $p->type = 'ArrayCollection<JMS\SerializerBundle\Tests\Fixtures\Comment>';
        $p->xmlCollection = true;
        $p->xmlCollectionInline = true;
        $p->xmlEntryName = 'comment';
        $p->groups = array("comments");
        $this->assertEquals($p, $m->propertyMetadata['comments']);

        $p = new PropertyMetadata($m->name, 'author');
        $p->type = 'JMS\SerializerBundle\Tests\Fixtures\Author';
        $p->groups = array("post");
        $this->assertEquals($p, $m->propertyMetadata['author']);

        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\Price'));
        $this->assertNotNull($m);

        $p = new PropertyMetadata($m->name, 'price');
        $p->type = 'double';
        $p->xmlValue = true;
        $this->assertEquals($p, $m->propertyMetadata['price']);
    }

    public function testVirtualProperty()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\ObjectWithVirtualProperties'));

        $this->assertArrayHasKey('existField', $m->propertyMetadata);
        $this->assertArrayHasKey('virtualValue', $m->propertyMetadata);
        $this->assertArrayHasKey('virtualSerializedValue', $m->propertyMetadata);

        $this->assertEquals($m->propertyMetadata['virtualSerializedValue']->serializedName, 'test', 'Serialized name is missing' );

        $p = new VirtualPropertyMetadata($m->name, 'virtualValue');
        $p->getter = 'getVirtualValue';

        $this->assertEquals($p, $m->propertyMetadata['virtualValue']);
    }

    public function testXmlKeyValuePairs()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\ObjectWithXmlKeyValuePairs'));

        $this->assertArrayHasKey('array', $m->propertyMetadata);
        $this->assertTrue($m->propertyMetadata['array']->xmlKeyValuePairs);
    }

    abstract protected function getDriver();
}
