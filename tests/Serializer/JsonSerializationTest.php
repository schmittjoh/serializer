<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\AuthorList;
use JMS\Serializer\Tests\Fixtures\FirstClassMapCollection;
use JMS\Serializer\Tests\Fixtures\ObjectWithEmptyArrayAndHash;
use JMS\Serializer\Tests\Fixtures\ObjectWithInlineArray;
use JMS\Serializer\Tests\Fixtures\Tag;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

class JsonSerializationTest extends BaseSerializationTest
{
    protected function getContent($key)
    {
        static $outputs = [];

        if (!$outputs) {
            $outputs['readonly'] = '{"id":123,"full_name":"Ruud Kamphuis"}';
            $outputs['string'] = '"foo"';
            $outputs['boolean_true'] = 'true';
            $outputs['boolean_false'] = 'false';
            $outputs['integer'] = '1';
            $outputs['float'] = '4.533';
            $outputs['float_trailing_zero'] = '1.0';
            $outputs['simple_object'] = '{"foo":"foo","moo":"bar","camel_case":"boo"}';
            $outputs['circular_reference'] = '{"collection":[{"name":"child1"},{"name":"child2"}],"another_collection":[{"name":"child1"},{"name":"child2"}]}';
            $outputs['circular_reference_collection'] = '{"name":"foo","collection":[]}';
            $outputs['array_strings'] = '["foo","bar"]';
            $outputs['array_booleans'] = '[true,false]';
            $outputs['array_integers'] = '[1,3,4]';
            $outputs['array_empty'] = '{"array":[]}';
            $outputs['array_floats'] = '[1.34,3.0,6.42]';
            $outputs['array_objects'] = '[{"foo":"foo","moo":"bar","camel_case":"boo"},{"foo":"baz","moo":"boo","camel_case":"boo"}]';
            $outputs['array_list_and_map_difference'] = '{"list":[1,2,3],"map":{"0":1,"2":2,"3":3}}';
            $outputs['array_mixed'] = '["foo",1,true,{"foo":"foo","moo":"bar","camel_case":"boo"},[1,3,true]]';
            $outputs['array_datetimes_object'] = '{"array_with_default_date_time":["2047-01-01T12:47:47+00:00","2016-12-05T00:00:00+00:00"],"array_with_formatted_date_time":["01.01.2047 12:47:47","05.12.2016 00:00:00"]}';
            $outputs['array_named_datetimes_object'] = '{"named_array_with_formatted_date":{"testdate1":"01.01.2047 12:47:47","testdate2":"05.12.2016 00:00:00"}}';
            $outputs['array_datetimes_object'] = '{"array_with_default_date_time":["2047-01-01T12:47:47+00:00","2016-12-05T00:00:00+00:00"],"array_with_formatted_date_time":["01.01.2047 12:47:47","05.12.2016 00:00:00"]}';
            $outputs['array_named_datetimes_object'] = '{"named_array_with_formatted_date":{"testdate1":"01.01.2047 12:47:47","testdate2":"05.12.2016 00:00:00"}}';
            $outputs['array_named_datetimeimmutables_object'] = '{"named_array_with_formatted_date":{"testdate1":"01.01.2047 12:47:47","testdate2":"05.12.2016 00:00:00"}}';
            $outputs['blog_post'] = '{"id":"what_a_nice_id","title":"This is a nice title.","created_at":"2011-07-30T00:00:00+00:00","is_published":false,"is_reviewed":false,"etag":"e86ce85cdb1253e4fc6352f5cf297248bceec62b","comments":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"comments2":[{"author":{"full_name":"Foo Bar"},"text":"foo"}],"metadata":{"foo":"bar"},"author":{"full_name":"Foo Bar"},"publisher":{"pub_name":"Bar Foo"},"tag":[{"name":"tag1"},{"name":"tag2"}]}';
            $outputs['blog_post_unauthored'] = '{"id":"what_a_nice_id","title":"This is a nice title.","created_at":"2011-07-30T00:00:00+00:00","is_published":false,"is_reviewed":false,"etag":"e86ce85cdb1253e4fc6352f5cf297248bceec62b","comments":[],"comments2":[],"metadata":{"foo":"bar"},"author":null,"publisher":null,"tag":null}';
            $outputs['price'] = '{"price":3.0}';
            $outputs['currency_aware_price'] = '{"currency":"EUR","amount":2.34}';
            $outputs['order'] = '{"cost":{"price":12.34}}';
            $outputs['order_with_currency_aware_price'] = '{"cost":{"currency":"EUR","amount":1.23}}';
            $outputs['log'] = '{"author_list":[{"full_name":"Johannes Schmitt"},{"full_name":"John Doe"}],"comments":[{"author":{"full_name":"Foo Bar"},"text":"foo"},{"author":{"full_name":"Foo Bar"},"text":"bar"},{"author":{"full_name":"Foo Bar"},"text":"baz"}]}';
            $outputs['lifecycle_callbacks'] = '{"name":"Foo Bar"}';
            $outputs['form_errors'] = '["This is the form error","Another error"]';
            $outputs['nested_form_errors'] = '{"errors":["This is the form error"],"children":{"bar":{"errors":["Error of the child form"]}}}';
            $outputs['constraint_violation'] = '{"property_path":"foo","message":"Message of violation"}';
            $outputs['constraint_violation_list'] = '[{"property_path":"foo","message":"Message of violation"},{"property_path":"bar","message":"Message of another violation"}]';
            $outputs['article'] = '{"custom":"serialized"}';
            $outputs['orm_proxy'] = '{"foo":"foo","moo":"bar","camel_case":"proxy-boo"}';
            $outputs['custom_accessor'] = '{"comments":{"Foo":{"comments":[{"author":{"full_name":"Foo"},"text":"foo"},{"author":{"full_name":"Foo"},"text":"bar"}],"count":2}}}';
            $outputs['mixed_access_types'] = '{"id":1,"name":"Johannes","read_only_property":42}';
            $outputs['accessor_order_child'] = '{"c":"c","d":"d","a":"a","b":"b"}';
            $outputs['accessor_order_parent'] = '{"a":"a","b":"b"}';
            $outputs['accessor_order_methods'] = '{"foo":"c","b":"b","a":"a"}';
            $outputs['inline'] = '{"c":"c","a":"a","b":"b","d":"d"}';
            $outputs['inline_child_empty'] = '{"c":"c","d":"d"}';
            $outputs['empty_child'] = '{"c":"c","d":"d","child":{}}';
            $outputs['empty_child_skip'] = '{"c":"c","d":"d"}';
            $outputs['groups_all'] = '{"foo":"foo","foobar":"foobar","bar":"bar","none":"none"}';
            $outputs['groups_foo'] = '{"foo":"foo","foobar":"foobar"}';
            $outputs['groups_foobar'] = '{"foo":"foo","foobar":"foobar","bar":"bar"}';
            $outputs['groups_default'] = '{"bar":"bar","none":"none"}';
            $outputs['groups_advanced'] = '{"name":"John","manager":{"name":"John Manager","friends":[{"nickname":"nickname"},{"nickname":"nickname"}]},"friends":[{"nickname":"nickname","manager":{"nickname":"nickname"}},{"nickname":"nickname","manager":{"nickname":"nickname"}}]}';
            $outputs['virtual_properties'] = '{"exist_field":"value","virtual_value":"value","test":"other-name","typed_virtual_property":1}';
            $outputs['virtual_properties_low'] = '{"low":1}';
            $outputs['virtual_properties_high'] = '{"high":8}';
            $outputs['virtual_properties_all'] = '{"low":1,"high":8}';
            $outputs['nullable'] = '{"foo":"bar","baz":null,"0":null}';
            $outputs['nullable_skip'] = '{"foo":"bar"}';
            $outputs['person_secret_show'] = '{"name":"mike","gender":"f"}';
            $outputs['person_secret_hide'] = '{"name":"mike"}';
            $outputs['null'] = 'null';
            $outputs['simple_object_nullable'] = '{"foo":"foo","moo":"bar","camel_case":"boo","null_property":null}';
            $outputs['input'] = '{"attributes":{"type":"text","name":"firstname","value":"Adrien"}}';
            $outputs['hash_empty'] = '{"hash":{}}';
            $outputs['object_when_null'] = '{"text":"foo"}';
            $outputs['object_when_null_and_serialized'] = '{"author":null,"text":"foo"}';
            $outputs['date_time'] = '"2011-08-30T00:00:00+00:00"';
            $outputs['date_time_immutable'] = '"2011-08-30T00:00:00+00:00"';
            $outputs['timestamp'] = '{"timestamp":1455148800}';
            $outputs['timestamp_prev'] = '{"timestamp":"1455148800"}';
            $outputs['date_interval'] = '"PT45M"';
            $outputs['car'] = '{"km":5,"type":"car"}';
            $outputs['car_without_type'] = '{"km":5}';
            $outputs['post'] = '{"type":"post","title":"Post Title"}';
            $outputs['image_post'] = '{"type":"image_post","title":"Image Post Title"}';
            $outputs['image_post_without_type'] = '{"title":"Image Post Title"}';
            $outputs['garage'] = '{"vehicles":[{"km":3,"type":"car"},{"km":1,"type":"moped"}]}';
            $outputs['tree'] = '{"tree":{"children":[{"children":[{"children":[],"foo":"bar"}],"foo":"bar"}],"foo":"bar"}}';
            $outputs['nullable_arrays'] = '{"empty_inline":[],"not_empty_inline":["not_empty_inline"],"empty_not_inline":[],"not_empty_not_inline":["not_empty_not_inline"],"empty_not_inline_skip":[],"not_empty_not_inline_skip":["not_empty_not_inline_skip"]}';
            $outputs['object_with_object_property_no_array_to_author'] = '{"foo": "bar", "author": "baz"}';
            $outputs['object_with_object_property'] = '{"foo": "bar", "author": {"full_name": "baz"}}';
            $outputs['author_expression'] = '{"my_first_name":"Ruud","last_name":"Kamphuis","id":123}';
            $outputs['author_expression_context'] = '{"first_name":"Ruud","direction":1,"name":"name"}';
            $outputs['maxdepth_skippabe_object'] = '{"a":{"xxx":"yyy"}}';
            $outputs['array_objects_nullable'] = '[]';
            $outputs['type_casting'] = '{"as_string":"8"}';
            $outputs['authors_inline'] = '[{"full_name":"foo"},{"full_name":"bar"}]';
            $outputs['inline_list_collection'] = '[1,2,3]';
            $outputs['inline_empty_list_collection'] = '[]';
            $outputs['inline_deserialization_list_collection'] = '[1,2]';
            $outputs['inline_map'] = '{"0":"1","1":"2","2":"3"}';
            $outputs['inline_empty_map'] = '{}';
            $outputs['empty_object'] = '{}';
            $outputs['inline_deserialization_map'] = '{"a":"b","c":"d","0":"5"}';
        }

        if (!isset($outputs[$key])) {
            throw new RuntimeException(sprintf('The key "%s" is not supported.', $key));
        }

        return $outputs[$key];
    }

    public function testSkipEmptyArrayAndHash()
    {
        $object = new ObjectWithEmptyArrayAndHash();

        self::assertEquals('{}', $this->serialize($object));
    }

    public function getFirstClassMapCollectionsValues()
    {
        return [
            [[1, 2, 3], $this->getContent('inline_map')],
            [[], $this->getContent('inline_empty_map')],
            [['a' => 'b', 'c' => 'd', 5], $this->getContent('inline_deserialization_map')],
        ];
    }

    /**
     * @param array $items
     * @param array $expected
     *
     * @dataProvider getFirstClassMapCollectionsValues
     */
    public function testFirstClassMapCollections($items, $expected): void
    {
        $collection = new FirstClassMapCollection($items);

        self::assertSame($expected, $this->serialize($collection));
        self::assertEquals(
            $collection,
            $this->deserialize($expected, get_class($collection))
        );
    }

    public function testAddLinksToOutput()
    {
        $this->dispatcher->addListener('serializer.post_serialize', static function (Event $event) {
            self::assertFalse($event->getVisitor()->hasData('_links'));
        }, 'JMS\Serializer\Tests\Fixtures\Author', 'json');

        $this->dispatcher->addSubscriber(new LinkAddingSubscriber());

        $this->dispatcher->addListener('serializer.post_serialize', static function (Event $event) {
            self::assertTrue($event->getVisitor()->hasData('_links'));
        }, 'JMS\Serializer\Tests\Fixtures\Author', 'json');

        $this->handlerRegistry->registerHandler(
            GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            'JMS\Serializer\Tests\Fixtures\AuthorList',
            'json',
            static function (SerializationVisitorInterface $visitor, AuthorList $data, array $type, Context $context) {
                return $visitor->visitArray(iterator_to_array($data), $type);
            }
        );

        $list = new AuthorList();
        $list->add(new Author('foo'));
        $list->add(new Author('bar'));

        self::assertEquals('[{"full_name":"foo","_links":{"details":"http:\/\/foo.bar\/details\/foo","comments":"http:\/\/foo.bar\/details\/foo\/comments"}},{"full_name":"bar","_links":{"details":"http:\/\/foo.bar\/details\/bar","comments":"http:\/\/foo.bar\/details\/bar\/comments"}}]', $this->serialize($list));
    }

    public function testReplaceNameInOutput()
    {
        $this->dispatcher->addSubscriber(new ReplaceNameSubscriber());
        $this->handlerRegistry->registerHandler(
            GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            'JMS\Serializer\Tests\Fixtures\AuthorList',
            'json',
            static function (SerializationVisitorInterface $visitor, AuthorList $data, array $type, Context $context) {
                return $visitor->visitArray(iterator_to_array($data), $type);
            }
        );

        $list = new AuthorList();
        $list->add(new Author('foo'));
        $list->add(new Author('bar'));

        self::assertEquals('[{"full_name":"new name"},{"full_name":"new name"}]', $this->serialize($list));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Invalid data "baz" (string), expected "JMS\Serializer\Tests\Fixtures\Author".
     */
    public function testDeserializingObjectWithObjectPropertyWithNoArrayToObject()
    {
        $content = $this->getContent('object_with_object_property_no_array_to_author');
        $object = $this->deserialize($content, 'JMS\Serializer\Tests\Fixtures\ObjectWithObjectProperty');
        self::assertEquals('bar', $object->getFoo());
        self::assertInstanceOf('JMS\Serializer\Tests\Fixtures\Author', $object->getAuthor());
    }

    public function testDeserializingObjectWithObjectProperty()
    {
        $content = $this->getContent('object_with_object_property');
        $object = $this->deserialize($content, 'JMS\Serializer\Tests\Fixtures\ObjectWithObjectProperty');
        self::assertEquals('bar', $object->getFoo());
        self::assertInstanceOf('JMS\Serializer\Tests\Fixtures\Author', $object->getAuthor());
        self::assertEquals('baz', $object->getAuthor()->getName());
    }

    public function getPrimitiveTypes()
    {
        return [
            [
                'type' => 'boolean',
                'data' => true,
            ],
            [
                'type' => 'integer',
                'data' => 123,
            ],
            [
                'type' => 'string',
                'data' => 'hello',
            ],
            [
                'type' => 'double',
                'data' => 0.1234,
            ],
        ];
    }

    /**
     * @dataProvider getPrimitiveTypes
     */
    public function testPrimitiveTypes($primitiveType, $data)
    {
        $navigator = $this->getMockBuilder(GraphNavigatorInterface::class)->getMock();

        $factory = new JsonSerializationVisitorFactory();
        $visitor = $factory->getVisitor();
        $visitor->setNavigator($navigator);
        $functionToCall = 'visit' . ucfirst($primitiveType);
        $result = $visitor->$functionToCall($data, [], $this->getMockBuilder(SerializationContext::class)->getMock());
        if ('double' === $primitiveType) {
            $primitiveType = 'float';
        }
        self::assertInternalType($primitiveType, $result);
    }

    /**
     * @group empty-object
     */
    public function testSerializeEmptyObject()
    {
        self::assertEquals('{}', $this->serialize(new Author(null)));
    }

    /**
     * @group encoding
     * @expectedException RuntimeException
     * @expectedExceptionMessage Your data could not be encoded because it contains invalid UTF8 characters.
     */
    public function testSerializeWithNonUtf8EncodingWhenDisplayErrorsOff()
    {
        ini_set('display_errors', '1');
        $this->serialize(['foo' => 'bar', 'bar' => pack('H*', 'c32e')]);
    }

    /**
     * @group encoding
     * @expectedException RuntimeException
     * @expectedExceptionMessage Your data could not be encoded because it contains invalid UTF8 characters.
     */
    public function testSerializeWithNonUtf8EncodingWhenDisplayErrorsOn()
    {
        ini_set('display_errors', '0');
        $this->serialize(['foo' => 'bar', 'bar' => pack('H*', 'c32e')]);
    }

    public function testSerializeArrayWithEmptyObject()
    {
        self::assertEquals('[{}]', $this->serialize([new \stdClass()]));
    }

    public function testInlineArray()
    {
        $object = new ObjectWithInlineArray(['a' => 'b', 'c' => 'd']);
        $serialized = $this->serialize($object);
        self::assertEquals('{"a":"b","c":"d"}', $serialized);
        self::assertEquals($object, $this->deserialize($serialized, ObjectWithInlineArray::class));
    }

    public function testSerializeRootArrayWithDefinedKeys()
    {
        $author1 = new Author('Jim');
        $author2 = new Author('Mark');

        $data = [
            'jim' => $author1,
            'mark' => $author2,
        ];

        self::assertEquals('{"jim":{"full_name":"Jim"},"mark":{"full_name":"Mark"}}', $this->serializer->serialize($data, $this->getFormat(), SerializationContext::create()->setInitialType('array')));
        self::assertEquals('[{"full_name":"Jim"},{"full_name":"Mark"}]', $this->serializer->serialize($data, $this->getFormat(), SerializationContext::create()->setInitialType('array<JMS\Serializer\Tests\Fixtures\Author>')));
        self::assertEquals('{"jim":{"full_name":"Jim"},"mark":{"full_name":"Mark"}}', $this->serializer->serialize($data, $this->getFormat(), SerializationContext::create()->setInitialType('array<string,JMS\Serializer\Tests\Fixtures\Author>')));

        $data = [
            $author1,
            $author2,
        ];
        self::assertEquals('[{"full_name":"Jim"},{"full_name":"Mark"}]', $this->serializer->serialize($data, $this->getFormat(), SerializationContext::create()->setInitialType('array')));
        self::assertEquals('{"0":{"full_name":"Jim"},"1":{"full_name":"Mark"}}', $this->serializer->serialize($data, $this->getFormat(), SerializationContext::create()->setInitialType('array<int,JMS\Serializer\Tests\Fixtures\Author>')));
        self::assertEquals('{"0":{"full_name":"Jim"},"1":{"full_name":"Mark"}}', $this->serializer->serialize($data, $this->getFormat(), SerializationContext::create()->setInitialType('array<string,JMS\Serializer\Tests\Fixtures\Author>')));
    }

    public function getTypeHintedArrays()
    {
        return [

            [[1, 2], '[1,2]', null],
            [['a', 'b'], '["a","b"]', null],
            [['a' => 'a', 'b' => 'b'], '{"a":"a","b":"b"}', null],

            [[], '[]', null],
            [[], '[]', SerializationContext::create()->setInitialType('array')],
            [[], '[]', SerializationContext::create()->setInitialType('array<integer>')],
            [[], '{}', SerializationContext::create()->setInitialType('array<string,integer>')],

            [[1, 2], '[1,2]', SerializationContext::create()->setInitialType('array')],
            [[1 => 1, 2 => 2], '{"1":1,"2":2}', SerializationContext::create()->setInitialType('array')],
            [[1 => 1, 2 => 2], '[1,2]', SerializationContext::create()->setInitialType('array<integer>')],
            [['a', 'b'], '["a","b"]', SerializationContext::create()->setInitialType('array<string>')],

            [[1 => 'a', 2 => 'b'], '["a","b"]', SerializationContext::create()->setInitialType('array<string>')],
            [['a' => 'a', 'b' => 'b'], '["a","b"]', SerializationContext::create()->setInitialType('array<string>')],

            [[1, 2], '{"0":1,"1":2}', SerializationContext::create()->setInitialType('array<integer,integer>')],
            [[1, 2], '{"0":1,"1":2}', SerializationContext::create()->setInitialType('array<string,integer>')],
            [[1, 2], '{"0":"1","1":"2"}', SerializationContext::create()->setInitialType('array<string,string>')],

            [['a', 'b'], '{"0":"a","1":"b"}', SerializationContext::create()->setInitialType('array<integer,string>')],
            [['a' => 'a', 'b' => 'b'], '{"a":"a","b":"b"}', SerializationContext::create()->setInitialType('array<string,string>')],
        ];
    }

    /**
     * @param array $array
     * @param string $expected
     * @param SerializationContext|null $context
     *
     * @dataProvider getTypeHintedArrays
     */
    public function testTypeHintedArraySerialization(array $array, $expected, $context = null)
    {
        self::assertEquals($expected, $this->serialize($array, $context));
    }

    public function getTypeHintedArraysAndStdClass()
    {
        $c1 = new \stdClass();
        $c2 = new \stdClass();
        $c2->foo = 'bar';

        $tag = new Tag('tag');

        $c3 = new \stdClass();
        $c3->foo = $tag;

        return [

            [[$c1], '[{}]', SerializationContext::create()->setInitialType('array<stdClass>')],

            [[$c2], '[{"foo":"bar"}]', SerializationContext::create()->setInitialType('array<stdClass>')],

            [[$tag], '[{"name":"tag"}]', SerializationContext::create()->setInitialType('array<JMS\Serializer\Tests\Fixtures\Tag>')],

            [[$c1], '{"0":{}}', SerializationContext::create()->setInitialType('array<integer,stdClass>')],
            [[$c2], '{"0":{"foo":"bar"}}', SerializationContext::create()->setInitialType('array<integer,stdClass>')],

            [[$c3], '{"0":{"foo":{"name":"tag"}}}', SerializationContext::create()->setInitialType('array<integer,stdClass>')],
            [[$c3], '[{"foo":{"name":"tag"}}]', SerializationContext::create()->setInitialType('array<stdClass>')],

            [[$tag], '{"0":{"name":"tag"}}', SerializationContext::create()->setInitialType('array<integer,JMS\Serializer\Tests\Fixtures\Tag>')],
        ];
    }

    /**
     * @param array $array
     * @param string $expected
     * @param SerializationContext|null $context
     *
     * @dataProvider getTypeHintedArraysAndStdClass
     */
    public function testTypeHintedArrayAndStdClassSerialization(array $array, $expected, $context = null)
    {
        self::assertEquals($expected, $this->serialize($array, $context));
    }

    protected function getFormat()
    {
        return 'json';
    }
}

class LinkAddingSubscriber implements EventSubscriberInterface
{
    public function onPostSerialize(Event $event)
    {
        $author = $event->getObject();

        $event->getVisitor()->setData('_links', [
            'details' => 'http://foo.bar/details/' . $author->getName(),
            'comments' => 'http://foo.bar/details/' . $author->getName() . '/comments',
        ]);
    }

    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.post_serialize', 'method' => 'onPostSerialize', 'format' => 'json', 'class' => 'JMS\Serializer\Tests\Fixtures\Author'],
        ];
    }
}

class ReplaceNameSubscriber implements EventSubscriberInterface
{
    public function onPostSerialize(Event $event)
    {
        $event->getVisitor()->setData('full_name', 'new name');
    }

    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.post_serialize', 'method' => 'onPostSerialize', 'format' => 'json', 'class' => 'JMS\Serializer\Tests\Fixtures\Author'],
        ];
    }
}
