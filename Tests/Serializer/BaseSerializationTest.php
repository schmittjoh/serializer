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

namespace JMS\SerializerBundle\Tests\Serializer;

use JMS\SerializerBundle\Serializer\Handler\DeserializationHandlerInterface;

use JMS\SerializerBundle\Tests\Fixtures\AuthorList;

use JMS\SerializerBundle\Serializer\VisitorInterface;

use JMS\SerializerBundle\Serializer\Handler\ArrayCollectionHandler;

use JMS\SerializerBundle\Serializer\XmlDeserializationVisitor;

use JMS\SerializerBundle\Serializer\Construction\UnserializeObjectConstructor;

use JMS\SerializerBundle\Serializer\JsonDeserializationVisitor;

use JMS\SerializerBundle\Tests\Fixtures\Log;

use JMS\SerializerBundle\Serializer\Handler\DateTimeHandler;
use JMS\SerializerBundle\Tests\Fixtures\Comment;
use JMS\SerializerBundle\Tests\Fixtures\Author;
use JMS\SerializerBundle\Tests\Fixtures\BlogPost;
use JMS\SerializerBundle\Tests\Fixtures\ObjectWithLifecycleCallbacks;
use JMS\SerializerBundle\Tests\Fixtures\CircularReferenceParent;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use Metadata\MetadataFactory;
use JMS\SerializerBundle\Tests\Fixtures\SimpleObject;
use JMS\SerializerBundle\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\SerializerBundle\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\SerializerBundle\Serializer\JsonSerializationVisitor;
use JMS\SerializerBundle\Serializer\Serializer;

abstract class BaseSerializationTest extends \PHPUnit_Framework_TestCase
{
    public function testString()
    {
        $this->assertEquals($this->getContent('string'), $this->serialize('foo'));
    }

    /**
     * @dataProvider getBooleans
     */
    public function testBooleans($strBoolean, $boolean)
    {
        $this->assertEquals($this->getContent('boolean_'.$strBoolean), $this->serialize($boolean));
    }

    public function getBooleans()
    {
        return array(array('true', true), array('false', false));
    }

    /**
     * @dataProvider getNumerics
     */
    public function testNumerics($key, $value)
    {
        $this->assertEquals($this->getContent($key), $this->serialize($value));
    }

    public function getNumerics()
    {
        return array(
            array('integer', 1),
            array('float', 4.533),
            array('float_trailing_zero', 1.0),
        );
    }

    public function testSimpleObject()
    {
        $this->assertEquals($this->getContent('simple_object'), $this->serialize(new SimpleObject('foo', 'bar')));
    }

    public function testArrayStrings()
    {
        $this->assertEquals($this->getContent('array_strings'), $this->serialize(array('foo', 'bar')));
    }

    public function testArrayBooleans()
    {
        $this->assertEquals($this->getContent('array_booleans'), $this->serialize(array(true, false)));
    }

    public function testArrayIntegers()
    {
        $this->assertEquals($this->getContent('array_integers'), $this->serialize(array(1, 3, 4)));
    }

    public function testArrayFloats()
    {
        $this->assertEquals($this->getContent('array_floats'), $this->serialize(array(1.34, 3.0, 6.42)));
    }

    public function testArrayObjects()
    {
        $this->assertEquals($this->getContent('array_objects'), $this->serialize(array(new SimpleObject('foo', 'bar'), new SimpleObject('baz', 'boo'))));
    }

    public function testArrayMixed()
    {
        $this->assertEquals($this->getContent('array_mixed'), $this->serialize(array('foo', 1, true, new SimpleObject('foo', 'bar'), array(1, 3, true))));
    }

    public function testBlogPost()
    {
        $post = new BlogPost('This is a nice title.', $author = new Author('Foo Bar'), new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC')));
        $post->addComment(new Comment($author, 'foo'));

        $this->assertEquals($this->getContent('blog_post'), $this->serialize($post));
    }

    /**
     * @group test
     */
    public function testLog()
    {
        $this->assertEquals($this->getContent('log'), $this->serialize($log = new Log()));

        $deserialized = $this->deserialize($this->getContent('log'), get_class($log));
        $this->assertEquals($log, $deserialized);
    }

    public function testCircularReference()
    {
        $object = new CircularReferenceParent();
        $this->assertEquals($this->getContent('circular_reference'), $this->serialize($object));

        $deserialized = $this->deserialize($this->getContent('circular_reference'), get_class($object));

        $col = $this->getField($deserialized, 'collection');
        $this->assertEquals(2, count($col));
        $this->assertEquals('child1', $col[0]->getName());
        $this->assertEquals('child2', $col[1]->getName());
        $this->assertSame($deserialized, $col[0]->getParent());
        $this->assertSame($deserialized, $col[1]->getParent());

        $col = $this->getField($deserialized, 'anotherCollection');
        $this->assertEquals(2, count($col));
        $this->assertEquals('child1', $col[0]->getName());
        $this->assertEquals('child2', $col[1]->getName());
        $this->assertSame($deserialized, $col[0]->getParent());
        $this->assertSame($deserialized, $col[1]->getParent());
    }

    public function testLifecycleCallbacks()
    {
        $object = new ObjectWithLifecycleCallbacks();
        $this->assertEquals($this->getContent('lifecycle_callbacks'), $this->serialize($object));
        $this->assertAttributeSame(null, 'name', $object);

        $deserialized = $this->deserialize($this->getContent('lifecycle_callbacks'), get_class($object));
        $this->assertEquals($object, $deserialized);
    }

    abstract protected function getContent($key);
    abstract protected function getFormat();

    protected function serialize($data)
    {
        return $this->getSerializer()->serialize($data, $this->getFormat());
    }

    protected function deserialize($content, $type)
    {
        return $this->getSerializer()->deserialize($content, $type, $this->getFormat());
    }

    protected function getSerializer()
    {
        $customHandlers = array();
        $customHandlers[] = new DateTimeHandler();

        $customDeserializationHandlers = $customHandlers;
        $customDeserializationHandlers[] = new ArrayCollectionHandler();
        $customDeserializationHandlers[] = new AuthorListDeserializationHandler();

        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $objectConstructor = new UnserializeObjectConstructor();

        $serializationVisitors = array(
            'json' => new JsonSerializationVisitor($namingStrategy, $customHandlers),
            'xml'  => new XmlSerializationVisitor($namingStrategy, $customHandlers),
        );
        $deserializationVisitors = array(
            'json' => new JsonDeserializationVisitor($namingStrategy, $customDeserializationHandlers, $objectConstructor),
            'xml'  => new XmlDeserializationVisitor($namingStrategy, $customDeserializationHandlers, $objectConstructor),
        );

        return new Serializer($factory, $serializationVisitors, $deserializationVisitors);
    }

    private function getField($obj, $name)
    {
        $ref = new \ReflectionProperty($obj, $name);
        $ref->setAccessible(true);

        return $ref->getValue($obj);
    }
}

class AuthorListDeserializationHandler implements DeserializationHandlerInterface
{
    public function deserialize(VisitorInterface $visitor, $data, $type, &$visited)
    {
        if ('AuthorList' !== $type) {
            return;
        }

        $visited = true;
        $elements = $visitor->getNavigator()->accept($data, 'array<integer, JMS\SerializerBundle\Tests\Fixtures\Author>', $visitor);
        $list = new AuthorList();
        foreach ($elements as $author) {
            $list->add($author);
        }

        return $list;
    }
}