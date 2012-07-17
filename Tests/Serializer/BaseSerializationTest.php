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

use JMS\SerializerBundle\Tests\Fixtures\AccessorOrderParent;
use JMS\SerializerBundle\Tests\Fixtures\AccessorOrderChild;
use JMS\SerializerBundle\Tests\Fixtures\GetSetObject;
use JMS\SerializerBundle\Tests\Fixtures\IndexedCommentsBlogPost;
use JMS\SerializerBundle\Tests\Fixtures\CurrencyAwareOrder;
use JMS\SerializerBundle\Tests\Fixtures\CurrencyAwarePrice;
use JMS\SerializerBundle\Tests\Fixtures\Order;
use Symfony\Component\Yaml\Inline;
use JMS\SerializerBundle\Serializer\YamlSerializationVisitor;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use JMS\SerializerBundle\Serializer\Handler\DeserializationHandlerInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;
use JMS\SerializerBundle\Tests\Fixtures\AuthorList;
use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\XmlDeserializationVisitor;
use JMS\SerializerBundle\Serializer\Construction\UnserializeObjectConstructor;
use JMS\SerializerBundle\Serializer\JsonDeserializationVisitor;
use JMS\SerializerBundle\Tests\Fixtures\Log;
use JMS\SerializerBundle\Serializer\Handler\ArrayCollectionHandler;
use JMS\SerializerBundle\Serializer\Handler\ObjectBasedCustomHandler;
use JMS\SerializerBundle\Serializer\Handler\DateTimeHandler;
use JMS\SerializerBundle\Serializer\Handler\FormErrorHandler;
use JMS\SerializerBundle\Serializer\Handler\ConstraintViolationHandler;
use JMS\SerializerBundle\Serializer\Handler\DoctrineProxyHandler;
use JMS\SerializerBundle\Tests\Fixtures\Comment;
use JMS\SerializerBundle\Tests\Fixtures\Author;
use JMS\SerializerBundle\Tests\Fixtures\AuthorReadOnly;
use JMS\SerializerBundle\Tests\Fixtures\BlogPost;
use JMS\SerializerBundle\Tests\Fixtures\ObjectWithLifecycleCallbacks;
use JMS\SerializerBundle\Tests\Fixtures\CircularReferenceParent;
use JMS\SerializerBundle\Tests\Fixtures\InlineParent;
use JMS\SerializerBundle\Tests\Fixtures\GroupsObject;
use JMS\SerializerBundle\Tests\Fixtures\ObjectWithVirtualProperties;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use Metadata\MetadataFactory;
use JMS\SerializerBundle\Tests\Fixtures\SimpleObject;
use JMS\SerializerBundle\Tests\Fixtures\SimpleObjectProxy;
use JMS\SerializerBundle\Tests\Fixtures\Price;
use JMS\SerializerBundle\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\SerializerBundle\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\SerializerBundle\Serializer\JsonSerializationVisitor;
use JMS\SerializerBundle\Serializer\Serializer;
use JMS\SerializerBundle\Tests\Fixtures\ObjectWithVersionedVirtualProperties;

abstract class BaseSerializationTest extends \PHPUnit_Framework_TestCase
{
    public function testString()
    {
        $this->assertEquals($this->getContent('string'), $this->serialize('foo'));

        if ($this->hasDeserializer()) {
            $this->assertEquals('foo', $this->deserialize($this->getContent('string'), 'string'));
        }
    }

    /**
     * @dataProvider getBooleans
     */
    public function testBooleans($strBoolean, $boolean)
    {
        $this->assertEquals($this->getContent('boolean_'.$strBoolean), $this->serialize($boolean));

        if ($this->hasDeserializer()) {
            $this->assertSame($boolean, $this->deserialize($this->getContent('boolean_'.$strBoolean), 'boolean'));
        }
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

        if ($this->hasDeserializer()) {
            $this->assertEquals($value, $this->deserialize($this->getContent($key), is_double($value) ? 'double' : 'integer'));
        }
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
        $this->assertEquals($this->getContent('simple_object'), $this->serialize($obj = new SimpleObject('foo', 'bar')));

        if ($this->hasDeserializer()) {
            $this->assertEquals($obj, $this->deserialize($this->getContent('simple_object'), get_class($obj)));
        }
    }

    public function testArrayStrings()
    {
        $data = array('foo', 'bar');
        $this->assertEquals($this->getContent('array_strings'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            $this->assertEquals($data, $this->deserialize($this->getContent('array_strings'), 'array<string>'));
        }
    }

    public function testArrayBooleans()
    {
        $data = array(true, false);
        $this->assertEquals($this->getContent('array_booleans'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            $this->assertEquals($data, $this->deserialize($this->getContent('array_booleans'), 'array<boolean>'));
        }
    }

    public function testArrayIntegers()
    {
        $data = array(1, 3, 4);
        $this->assertEquals($this->getContent('array_integers'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            $this->assertEquals($data, $this->deserialize($this->getContent('array_integers'), 'array<integer>'));
        }
    }

    public function testArrayFloats()
    {
        $data = array(1.34, 3.0, 6.42);
        $this->assertEquals($this->getContent('array_floats'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            $this->assertEquals($data, $this->deserialize($this->getContent('array_floats'), 'array<double>'));
        }
    }

    public function testArrayObjects()
    {
        $data = array(new SimpleObject('foo', 'bar'), new SimpleObject('baz', 'boo'));
        $this->assertEquals($this->getContent('array_objects'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            $this->assertEquals($data, $this->deserialize($this->getContent('array_objects'), 'array<JMS\SerializerBundle\Tests\Fixtures\SimpleObject>'));
        }
    }

    public function testArrayMixed()
    {
        $this->assertEquals($this->getContent('array_mixed'), $this->serialize(array('foo', 1, true, new SimpleObject('foo', 'bar'), array(1, 3, true))));
    }

    public function testBlogPost()
    {
        $post = new BlogPost('This is a nice title.', $author = new Author('Foo Bar'), new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC')));
        $post->addComment($comment = new Comment($author, 'foo'));

        $this->assertEquals($this->getContent('blog_post'), $this->serialize($post));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('blog_post'), get_class($post));
            $this->assertEquals('2011-07-30T00:00:00+0000', $this->getField($deserialized, 'createdAt')->format(\DateTime::ISO8601));
            $this->assertAttributeEquals('This is a nice title.', 'title', $deserialized);
            $this->assertAttributeSame(false, 'published', $deserialized);
            $this->assertAttributeEquals(new ArrayCollection(array($comment)), 'comments', $deserialized);
            $this->assertAttributeEquals($author, 'author', $deserialized);
        }
    }

    public function testReadOnly()
    {
        $author = new AuthorReadOnly(123, 'Ruud Kamphuis');
        $this->assertEquals($this->getContent('readonly'), $this->serialize($author));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('readonly'), get_class($author));
            $this->assertNull($this->getField($deserialized, 'id'));
            $this->assertEquals('Ruud Kamphuis', $this->getField($deserialized, 'name'));
        }
    }

    public function testPrice()
    {
        $price = new Price(3);
        $this->assertEquals($this->getContent('price'), $this->serialize($price));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('price'), get_class($price));
            $this->assertEquals(3, $this->getField($deserialized, 'price'));
        }
    }

    public function testOrder()
    {
        $order = new Order(new Price(12.34));
        $this->assertEquals($this->getContent('order'), $this->serialize($order));

        if ($this->hasDeserializer()) {
            $this->assertEquals($order, $this->deserialize($this->getContent('order'), get_class($order)));
        }
    }

    public function testCurrencyAwarePrice()
    {
        $price = new CurrencyAwarePrice(2.34);
        $this->assertEquals($this->getContent('currency_aware_price'), $this->serialize($price));

        if ($this->hasDeserializer()) {
            $this->assertEquals($price, $this->deserialize($this->getContent('currency_aware_price'), get_class($price)));
        }
    }

    public function testOrderWithCurrencyAwarePrice()
    {
        $order = new CurrencyAwareOrder(new CurrencyAwarePrice(1.23));
        $this->assertEquals($this->getContent('order_with_currency_aware_price'), $this->serialize($order));

        if ($this->hasDeserializer()) {
            $this->assertEquals($order, $this->deserialize($this->getContent('order_with_currency_aware_price'), get_class($order)));
        }
    }

    public function testArticle()
    {
        $article = new Article();
        $article->element = 'custom';
        $article->value = 'serialized';

        $result = $this->serialize($article);
        $this->assertEquals($this->getContent('article'), $result);

        if ($this->hasDeserializer()) {
            $this->assertEquals($article, $this->deserialize($result, 'JMS\SerializerBundle\Tests\Serializer\Article'));
        }
    }

    public function testInline()
    {
        $inline = new InlineParent();

        $result = $this->serialize($inline);
        $this->assertEquals($this->getContent('inline'), $result);

        //no deserialization support
        /*if ($this->hasDeserializer()) {
            $this->assertEquals($inline, $this->deserialize($result, 'JMS\SerializerBundle\Tests\Serializer\InlineParent'));
        }*/
    }

    /**
     * @group test
     */
    public function testLog()
    {
        $this->assertEquals($this->getContent('log'), $this->serialize($log = new Log()));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('log'), get_class($log));
            $this->assertEquals($log, $deserialized);
        }
    }

    public function testCircularReference()
    {
        $object = new CircularReferenceParent();
        $this->assertEquals($this->getContent('circular_reference'), $this->serialize($object));

        if ($this->hasDeserializer()) {
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
    }

    public function testLifecycleCallbacks()
    {
        $object = new ObjectWithLifecycleCallbacks();
        $this->assertEquals($this->getContent('lifecycle_callbacks'), $this->serialize($object));
        $this->assertAttributeSame(null, 'name', $object);

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('lifecycle_callbacks'), get_class($object));
            $this->assertEquals($object, $deserialized);
        }
    }

    public function testFormErrors()
    {
        $errors = array(
            new FormError('This is the form error'),
            new FormError('Another error')
        );

        $this->assertEquals($this->getContent('form_errors'), $this->serialize($errors));
    }

    public function testNestedFormErrors()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $formConfig = $this->getMock('Symfony\Component\Form\FormConfigInterface');
        $formConfig->expects($this->any())
            ->method('getEventDispatcher')
            ->will($this->returnValue($dispatcher));
        $formConfig->expects($this->any())
            ->method('getModelTransformers')
            ->will($this->returnValue(array()));

        $fooConfig = clone $formConfig;
        $fooConfig->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $form = new Form($fooConfig);
        $form->addError(new FormError('This is the form error'));

        $barConfig = clone $formConfig;
        $barConfig->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $child = new Form($barConfig);
        $child->addError(new FormError('Error of the child form'));
        $form->add($child);

        $this->assertEquals($this->getContent('nested_form_errors'), $this->serialize($form));
    }

    public function testConstraintViolation()
    {
        $violation = new ConstraintViolation('Message of violation', array(), null, 'foo', null);

        $this->assertEquals($this->getContent('constraint_violation'), $this->serialize($violation));
    }

    public function testConstraintViolationList()
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation('Message of violation', array(), null, 'foo', null));
        $violations->add(new ConstraintViolation('Message of another violation', array(), null, 'bar', null));

        $this->assertEquals($this->getContent('constraint_violation_list'), $this->serialize($violations));
    }

    public function testDoctrineProxy()
    {
        if (!class_exists('Doctrine\ORM\Version')) {
            $this->markTestSkipped('Doctrine is not available.');
        }

        $object = new SimpleObjectProxy('foo', 'bar');

        $this->assertEquals($this->getContent('orm_proxy'), $this->serialize($object));
    }

    public function testInitializedDoctrineProxy()
    {
        if (!class_exists('Doctrine\ORM\Version')) {
            $this->markTestSkipped('Doctrine is not available.');
        }

        $object = new SimpleObjectProxy('foo', 'bar');
        $object->__load();

        $this->assertEquals($this->getContent('orm_proxy'), $this->serialize($object));
    }

    public function testCustomAccessor()
    {
        $post = new IndexedCommentsBlogPost();

        $this->assertEquals($this->getContent('custom_accessor'), $this->serialize($post));
    }

    public function testMixedAccessTypes()
    {
        $object = new GetSetObject();

        $this->assertEquals($this->getContent('mixed_access_types'), $this->serialize($object));

        if ($this->hasDeserializer()) {
            $object = $this->deserialize($this->getContent('mixed_access_types'), 'JMS\SerializerBundle\Tests\Fixtures\GetSetObject');
            $this->assertAttributeEquals(1, 'id', $object);
            $this->assertAttributeEquals('Johannes', 'name', $object);
            $this->assertAttributeEquals(42, 'readOnlyProperty', $object);
        }
    }

    public function testAccessorOrder()
    {
        $this->assertEquals($this->getContent('accessor_order_child'), $this->serialize(new AccessorOrderChild()));
        $this->assertEquals($this->getContent('accessor_order_parent'), $this->serialize(new AccessorOrderParent()));
    }

    public function testGroups()
    {
        $serializer =  $this->getSerializer();

        $groupsObject = new GroupsObject();

        $this->assertEquals($this->getContent('groups_all'), $serializer->serialize($groupsObject, $this->getFormat()));

        $serializer->setGroups(array("foo"));
        $this->assertEquals($this->getContent('groups_foo'), $serializer->serialize($groupsObject, $this->getFormat()));

        $serializer->setGroups(array("foo", "bar"));
        $this->assertEquals($this->getContent('groups_foobar'), $serializer->serialize($groupsObject, $this->getFormat()));

        $serializer->setGroups(null);
        $this->assertEquals($this->getContent('groups_all'), $serializer->serialize($groupsObject, $this->getFormat()));

        $serializer->setGroups(array());
        $this->assertEquals($this->getContent('groups_all'), $serializer->serialize($groupsObject, $this->getFormat()));
    }

    public function testVirtualProperty()
    {
        $this->assertEquals($this->getContent('virtual_properties'), $this->serialize(new ObjectWithVirtualProperties()));
    }

    public function testVirtualVersions()
    {
        $serializer = $this->getSerializer();

        $serializer->setVersion(2);
        $this->assertEquals($this->getContent('virtual_properties_low'), $serializer->serialize(new ObjectWithVersionedVirtualProperties(), $this->getFormat()));

        $serializer->setVersion(7);
        $this->assertEquals($this->getContent('virtual_properties_all'), $serializer->serialize(new ObjectWithVersionedVirtualProperties(), $this->getFormat()));

        $serializer->setVersion(9);
        $this->assertEquals($this->getContent('virtual_properties_high'), $serializer->serialize(new ObjectWithVersionedVirtualProperties(), $this->getFormat()));
    }

    abstract protected function getContent($key);
    abstract protected function getFormat();

    protected function hasDeserializer()
    {
        return true;
    }

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
        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $objectConstructor = new UnserializeObjectConstructor();

        $customSerializationHandlers = $this->getSerializationHandlers();
        $customDeserializationHandlers = $this->getDeserializationHandlers();

        $serializationVisitors = array(
            'json' => new JsonSerializationVisitor($namingStrategy, $customSerializationHandlers),
            'xml'  => new XmlSerializationVisitor($namingStrategy, $customSerializationHandlers),
            'yml'  => new YamlSerializationVisitor($namingStrategy, $customSerializationHandlers),
        );
        $deserializationVisitors = array(
            'json' => new JsonDeserializationVisitor($namingStrategy, $customDeserializationHandlers, $objectConstructor),
            'xml'  => new XmlDeserializationVisitor($namingStrategy, $customDeserializationHandlers, $objectConstructor),
        );

        return new Serializer($factory, $serializationVisitors, $deserializationVisitors);
    }

    protected function getSerializationHandlers()
    {
        $translatorMock = $this->getMock('Symfony\\Component\\Translation\\TranslatorInterface');
        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->will($this->returnArgument(0));

        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $objectConstructor = new UnserializeObjectConstructor();

        $handlers = array(
            new ObjectBasedCustomHandler($objectConstructor, $factory),
            new DateTimeHandler(),
            new FormErrorHandler($translatorMock),
            new ConstraintViolationHandler(),
            new DoctrineProxyHandler(),
        );

        return $handlers;
    }

    protected function getDeserializationHandlers()
    {
        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $objectConstructor = new UnserializeObjectConstructor();

        $handlers = array(
            new ObjectBasedCustomHandler($objectConstructor, $factory),
            new DateTimeHandler(),
            new ArrayCollectionHandler(),
            new AuthorListDeserializationHandler(),
        );

        return $handlers;
    }

    private function getField($obj, $name)
    {
        $ref = new \ReflectionProperty($obj, $name);
        $ref->setAccessible(true);

        return $ref->getValue($obj);
    }

    private function setField($obj, $name, $value)
    {
        $ref = new \ReflectionProperty($obj, $name);
        $ref->setAccessible(true);
        $ref->setValue($obj, $value);
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

class Article implements SerializationHandlerInterface, DeserializationHandlerInterface
{
    public $element;
    public $value;

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {
        if (!$data instanceof Article) {
            return;
        }

        if ($visitor instanceof XmlSerializationVisitor) {
            $visited = true;

            if (null === $visitor->document) {
                $visitor->document = $visitor->createDocument(null, null, false);
            }

            $visitor->document->appendChild($visitor->document->createElement($this->element, $this->value));
        } elseif ($visitor instanceof JsonSerializationVisitor) {
            $visited = true;

            $visitor->setRoot(array($this->element => $this->value));
        } elseif ($visitor instanceof YamlSerializationVisitor) {
            $visited = true;

            $visitor->writer->writeln(Inline::dump($this->element).': '.Inline::dump($this->value));
        }
    }

    public function deserialize(VisitorInterface $visitor, $data, $type, &$visited)
    {
        if ('JMS\SerializerBundle\Tests\Serializer\Article' !== $type) {
            return;
        }

        if ($visitor instanceof XmlDeserializationVisitor) {
            $visited = true;

            $this->element = $data->getName();
            $this->value = (string)$data;
        } elseif ($visitor instanceof JsonDeserializationVisitor) {
            $visited = true;

            $this->element = key($data);
            $this->value = reset($data);
        }

        return $this;
    }
}
