<?php
/*
 * Copyright 2015 Ivan Borzenkov <ivan.borzenkov@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\Context;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use JMS\Serializer\Handler\ConstraintViolationHandler;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\FormErrorHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\PhpCollectionHandler;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\Tests\Fixtures\AuthorList;
use JMS\Serializer\VisitorInterface;
use Metadata\MetadataFactory;
use PhpCollection\Map;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\MessageSelector;

class DeserializeExceptionTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;
    protected $dispatcher;

    /** @var Serializer */
    protected $serializer;
    protected $handlerRegistry;
    protected $serializationVisitors;
    protected $deserializationVisitors;

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path ".": expected boolean, but got array: {"t":"t"}
     */
    public function testBoolean()
    {
        $this->deserialize($this->getContent('object'), 'boolean');
    }

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path ".": expected string, but got array: {"t":"t"}
     */
    public function testString()
    {
        $this->deserialize($this->getContent('object'), 'string');
    }

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path ".": expected integer, but got array: {"t":"t"}
     */
    public function testInteger()
    {
        $this->deserialize($this->getContent('object'), 'integer');
    }

    public function testNumericStringConvert()
    {
        $this->assertEquals($this->deserialize($this->getContent('numeric'), 'integer'), 1);
    }

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path ".": expected float, but got array: {"t":"t"}
     */
    public function testDouble()
    {
        $this->deserialize($this->getContent('object'), 'float');
    }

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path ".": expected array, but got integer: 1
     */
    public function testArray()
    {
        $this->deserialize($this->getContent('scalar'), 'array');
    }

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path ".": expected object, but got integer: 1
     */
    public function testProperty()
    {
        $this->deserialize($this->getContent('scalar'), 'JMS\Serializer\Tests\Fixtures\BlogPost');
    }

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path "id": expected string, but got array: {"t":"t"}
     */
    public function testPathId()
    {
        $post = $this->deserialize($this->getContent('blog_post_id'), 'JMS\Serializer\Tests\Fixtures\BlogPost');
    }

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path "publisher.pub_name": expected string, but got array: {"t":"t"}
     */
    public function testPathPublisher()
    {
        $post = $this->deserialize($this->getContent('blog_post_publisher'), 'JMS\Serializer\Tests\Fixtures\BlogPost');
    }

    /**
     * @expectedException \JMS\Serializer\Exception\DeserializeException
     * @expectedExceptionMessage Path "comments[0].author.full_name": expected string, but got array: {"t":"t"}
     */
    public function testPathArray()
    {
        $post = $this->deserialize($this->getContent('blog_post_array'), 'JMS\Serializer\Tests\Fixtures\BlogPost');
    }

    protected function deserialize($content, $type, Context $context = null)
    {
        return $this->serializer->deserialize($content, $type, 'json', $context);
    }

    protected function getContent($key)
    {
        static $outputs = array();

        if (!$outputs) {
            $outputs['scalar'] = '1';
            $outputs['numeric'] = '"1"';
            $outputs['object'] = '{ "t": "t" }';
            $outputs['blog_post'] = '{"id":"id","title":"This is a nice title.","created_at":"2011-07-30T00:00:00+0000","is_published":false,"etag":"1edf9bf60a32d89afbb85b2be849e3ceed5f5b10","comments":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"comments2":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"metadata":{"foo":"bar"},"author":{"full_name":"Foo Bar"},"publisher":{"pub_name":"Bar Foo"}}';
            $outputs['blog_post_id'] = '{"id":{ "t": "t" },"title":"This is a nice title.","created_at":"2011-07-30T00:00:00+0000","is_published":false,"etag":"1edf9bf60a32d89afbb85b2be849e3ceed5f5b10","comments":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"comments2":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"metadata":{"foo":"bar"},"author":{"full_name":"Foo Bar"},"publisher":{"pub_name":"Bar Foo"}}';
            $outputs['blog_post_publisher'] = '{"id":"id","title":"This is a nice title.","created_at":"2011-07-30T00:00:00+0000","is_published":false,"etag":"1edf9bf60a32d89afbb85b2be849e3ceed5f5b10","comments":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"comments2":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"metadata":{"foo":"bar"},"author":{"full_name":"Foo Bar"},"publisher":{"pub_name":{ "t": "t" }}}';
            $outputs['blog_post_array'] = '{"id":"id","title":"This is a nice title.","created_at":"2011-07-30T00:00:00+0000","is_published":false,"etag":"1edf9bf60a32d89afbb85b2be849e3ceed5f5b10","comments":[{"author":{"full_name":{ "t": "t" }},"text":"foo"}],"comments2":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"metadata":{"foo":"bar"},"author":{"full_name":"Foo Bar"},"publisher":{"pub_name":"Bar Foo"}}';
        }

        if (!isset($outputs[$key])) {
            throw new RuntimeException(sprintf('The key "%s" is not supported.', $key));
        }

        return $outputs[$key];
    }

    protected function getFormat()
    {
        return 'json';
    }

    protected function setUp()
    {
        $this->factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));

        $this->handlerRegistry = new HandlerRegistry();
        $this->handlerRegistry->registerSubscribingHandler(new ConstraintViolationHandler());
        $this->handlerRegistry->registerSubscribingHandler(new DateHandler());
        $this->handlerRegistry->registerSubscribingHandler(new FormErrorHandler(new IdentityTranslator(new MessageSelector())));
        $this->handlerRegistry->registerSubscribingHandler(new PhpCollectionHandler());
        $this->handlerRegistry->registerSubscribingHandler(new ArrayCollectionHandler());
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_SERIALIZATION, 'AuthorList', $this->getFormat(),
            function(VisitorInterface $visitor, $object, array $type, Context $context) {
                return $visitor->visitArray(iterator_to_array($object), $type, $context);
            }
        );
        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_DESERIALIZATION, 'AuthorList', $this->getFormat(),
            function(VisitorInterface $visitor, $data, $type, Context $context) {
                $type = array(
                    'name' => 'array',
                    'params' => array(
                        array('name' => 'integer', 'params' => array()),
                        array('name' => 'JMS\Serializer\Tests\Fixtures\Author', 'params' => array()),
                    ),
                );

                $elements = $visitor->getNavigator()->accept($data, $type, $context);
                $list = new AuthorList();
                foreach ($elements as $author) {
                    $list->add($author);
                }

                return $list;
            }
        );

        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $objectConstructor = new UnserializeObjectConstructor();
        $this->serializationVisitors = new Map(array(
            'json' => new JsonSerializationVisitor($namingStrategy),
        ));
        $this->deserializationVisitors = new Map(array(
            'json' => new JsonDeserializationVisitor($namingStrategy),
        ));

        $this->serializer = new Serializer($this->factory, $this->handlerRegistry, $objectConstructor, $this->serializationVisitors, $this->deserializationVisitors, $this->dispatcher);
    }
}
