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

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Exception\RuntimeException;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ArraySerializationTest extends BaseSerializationTest
{
    protected function getFormat()
    {
        return 'array';
    }

    protected function getContent($key)
    {
        $output = array();
        $output['readonly'] = array('id' => 123, 'full_name' => 'Ruud Kamphuis');
        $output['string'] = 'foo';
        $output['boolean_true'] = true;
        $output['boolean_false'] = false;
        $output['integer'] = 1;
        $output['float'] = 4.533;
        $output['float_trailing_zero'] = 1;
        $output['simple_object'] = array('foo' => 'foo', 'moo' => 'bar', 'camel_case' => 'boo');
        $output['circular_reference'] = array(
            'collection' => array(array('name' => 'child1'), array('name' => 'child2')),
            'another_collection' => array(array('name' => 'child1'), array('name' => 'child2'))
        );
        $output['array_strings'] = array('foo', 'bar');
        $output['array_booleans'] = array(true, false);
        $output['array_integers'] = array(1, 3, 4);
        $output['array_floats'] = array(1.34, 3, 6.42);
        $output['array_objects'] = array(
            array('foo' => 'foo', 'moo' => 'bar', 'camel_case' => 'boo'),
            array('foo' => 'baz', 'moo' => 'boo', 'camel_case' => 'boo')
        );
        $output['array_mixed'] = array(
            'foo',
            1,
            true,
            array('foo' => 'foo', 'moo' => 'bar', 'camel_case' => 'boo',), array(1, 3, true));
        $output['blog_post'] = array(
            'title' => 'This is a nice title.',
            'created_at' => '2011-07-30T00:00:00+0000',
            'is_published' => false,
            'comments' => array(array('author' => array('full_name' => 'Foo Bar'), 'text' => 'foo')),
            'comments2' => array(array('author' => array('full_name' => 'Foo Bar'), 'text' => 'foo')),
            'author' => array('full_name' => 'Foo Bar'),
        );
        $output['blog_post_unauthored'] = array(
            'title' => 'This is a nice title.',
            'created_at' => '2011-07-30T00:00:00+0000',
            'is_published' => false,
            'comments' => array(),
            'comments2' => array(),
            'author' => null,
        );
        $output['price'] = array('price' => 3);
        $output['currency_aware_price'] = array('currency' => 'EUR', 'amount' => 2.34);
        $output['order'] = array('cost' => array('price' => 12.34));
        $output['order_with_currency_aware_price'] = array('cost' => array('currency' => 'EUR', 'amount' => 1.23));
        $output['log'] = array(
            'author_list' => array(array('full_name' => 'Johannes Schmitt'), array('full_name' => 'John Doe')),
            'comments' => array(
                array('author' => array('full_name' => 'Foo Bar'), 'text' => 'foo'),
                array('author' => array('full_name' => 'Foo Bar'), 'text' => 'bar'),
                array('author' => array('full_name' => 'Foo Bar'), 'text' => 'baz'),
            ),
        );
        $output['lifecycle_callbacks'] = array('name' => 'Foo Bar');
        $output['form_errors'] = array('This is the form error', 'Another error');
        $output['nested_form_errors'] = array(
            'errors' => array('This is the form error'),
            'children' => array(
                'bar' => array(
                    'errors' => array(
                        'Error of the child form',
                    ),
                ),
            ),
        );
        $output['constraint_violation'] = array('property_path' => 'foo', 'message' => 'Message of violation');
        $output['constraint_violation_list'] = array(
            array('property_path' => 'foo', 'message' => 'Message of violation'),
            array('property_path' => 'bar', 'message' => 'Message of another violation'),
        );
        $output['article'] = array('custom' => 'serialized');
        $output['orm_proxy'] = array('foo' => 'foo', 'moo' => 'bar', 'camel_case' => 'proxy-boo');
        $output['custom_accessor'] = array(
            'comments' => array(
                'Foo' => array(
                    'comments' => array(
                        array('author' => array('full_name' => 'Foo'), 'text' => 'foo'),
                        array('author' => array('full_name' => 'Foo'), 'text' => 'bar')
                    ),
                    'count' => 2,
                ),
            ),
        );
        $output['mixed_access_types'] = array('id' => 1, 'name' => 'Johannes', 'read_only_property' => 42);
        $output['accessor_order_child'] = array('c' => 'c', 'd' => 'd', 'a' => 'a', 'b' => 'b');
        $output['accessor_order_parent'] = array('a' => 'a', 'b' => 'b');
        $output['inline'] = array('c' => 'c', 'a' => 'a', 'b' => 'b', 'd' => 'd');
        $output['groups_all'] = array('foo' => 'foo', 'foobar' => 'foobar', 'bar' => 'bar', 'none' => 'none');
        $output['groups_foo'] = array('foo' => 'foo', 'foobar' => 'foobar');
        $output['groups_foobar'] = array('foo' => 'foo', 'foobar' => 'foobar', 'bar' => 'bar');
        $output['groups_default'] = array('bar' => 'bar', 'none' => 'none');
        $output['virtual_properties'] = array('exist_field' => 'value', 'virtual_value' => 'value', 'test' => 'other-name');
        $output['virtual_properties_low'] = array('low' => 1);
        $output['virtual_properties_high'] = array('high' => 8);
        $output['virtual_properties_all'] = array('low' => 1, 'high' => 8);
        $output['nullable'] = array('foo' => 'bar', 'baz' => null);
        $output['null'] = null;
        $output['simple_object_nullable'] = array('foo' => 'foo', 'moo' => 'bar', 'camel_case' => 'boo', 'null_property' => null);
        $output['input'] = array('attributes' => array('type' => 'text', 'name' => 'firstname', 'value' => 'Adrien'));
        $output['hash_empty'] = array('hash' => array());
        $output['object_when_null'] = array('text' => 'foo');
        $output['object_when_null_and_serialized'] = array('author' => null, 'text' => 'foo');
        $output['date_time'] = '2011-08-30T00:00:00+0000';
        $output['date_interval'] = 'PT45M';

        if (!isset($output[$key]) && $key !== 'null') {
            throw new RuntimeException(sprintf('The key "%s" is not supported.', $key));
        }

        return $output[$key];
    }
}
