<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

namespace JMS\Serializer\Tests\Metadata;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\ClassMetadata;
use RuntimeException;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function getAccessOrderCases()
    {
        return [
            [array('b', 'a'), array('b', 'a')],
            [array('a', 'b'), array('a', 'b')],
            [array('b'), array('b', 'a')],
            [array('a'), array('a', 'b')],
            [array('foo', 'bar'), array('b', 'a')],
        ];
    }

    /**
     * @dataProvider getAccessOrderCases
     */
    public function testSetAccessorOrderCustom(array $order, array $expected)
    {
        $metadata = new ClassMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder');
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'b'));
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'a'));
        $this->assertEquals(array('b', 'a'), array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_CUSTOM, $order);
        $this->assertEquals($expected, array_keys($metadata->propertyMetadata));
    }

    public function testSetAccessorOrderAlphabetical()
    {
        $metadata = new ClassMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder');
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'b'));
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'a'));
        $this->assertEquals(array('b', 'a'), array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_ALPHABETICAL);
        $this->assertEquals(array('a', 'b'), array_keys($metadata->propertyMetadata));
    }

    /**
     * @dataProvider providerPublicMethodData
     */
    public function testAccessorTypePublicMethod($property, $getterInit, $setterInit, $getterName, $setterName)
    {
        $object = new PropertyMetadataPublicMethod();

        $metadata = new PropertyMetadata(get_class($object), $property);
        $metadata->setAccessor(PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD, $getterInit, $setterInit);

        $this->assertEquals($getterName, $metadata->getter);
        $this->assertEquals($setterName, $metadata->setter);

        // setter is not supported by setValue(), any idea?
        $object->{$metadata->setter}('x');

        $this->assertEquals(sprintf('%1$s:%1$s:x', strtoupper($property)), $metadata->getValue($object));
    }

    /**
     * @dataProvider providerPublicMethodException
     */
    public function testAccessorTypePublicMethodException($getter, $setter, $message)
    {
        $this->setExpectedException('\JMS\Serializer\Exception\RuntimeException', $message);

        $object = new PropertyMetadataPublicMethod();

        $metadata = new PropertyMetadata(get_class($object), 'e');
        $metadata->setAccessor(PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD, $getter, $setter);
    }

    public function providerPublicMethodData()
    {
        return array(
            array('a', null, null, 'geta', 'seta'),
            array('b', null, null, 'isb', 'setb'),
            array('c', null, null, 'hasc', 'setc'),
            array('d', 'fetchd', 'saved', 'fetchd', 'saved')
        );
    }

    public function providerPublicMethodException()
    {
        return array(
            array(null, null, 'a public getE method, nor a public isE method, nor a public hasE method in class'),
            array(null, 'setx', 'a public getE method, nor a public isE method, nor a public hasE method in class'),
            array('getx', null, 'no public setE method in class'),
        );
    }

    /**
     * Test if it is possible to serialize & deserialize a ClassMetadata instance
     */
    public function testSerializeValid()
    {
        $classMetadata = new ClassMetadata(PropertyMetadataOrder::class);
        unserialize(serialize($classMetadata));
    }

    /**
     * Test if an older cache generates the right exception if it gets deserialized.
     *
     * @expectedException RuntimeException
     * @expectedExceptionMessage The jms serializer cache is invalid. Clear the cache
     */
    public function testSerializeInvalid()
    {
        unserialize('C:37:"JMS\Serializer\Metadata\ClassMetadata":251:{a:15:{i:0;a:0:{}i:1;a:0:{}i:2;a:0:{}i:3;N;i:4;N;i:5;a:0:{}i:6;N;i:7;N;i:8;a:0:{}i:9;b:0;i:10;N;i:11;N;i:12;N;i:13;a:0:{}i:14;s:116:"a:5:{i:0;s:51:"JMS\Serializer\Tests\Metadata\PropertyMetadataOrder";i:1;a:0:{}i:2;a:0:{}i:3;a:0:{}i:4;i:1463051331;}";}}');
    }
}

class PropertyMetadataOrder
{
    private $b, $a;
}

class PropertyMetadataPublicMethod
{
    private $a, $b, $c, $d, $e;

    public function getA()
    {
        return 'A:' . $this->a;
    }

    public function setA($a)
    {
        $this->a = 'A:' . $a;
    }

    public function isB()
    {
        return 'B:' . $this->b;
    }

    public function setB($b)
    {
        $this->b = 'B:' . $b;
    }

    public function hasC()
    {
        return 'C:' . $this->c;
    }

    public function setC($c)
    {
        $this->c = 'C:' . $c;
    }

    public function fetchD()
    {
        return 'D:' . $this->d;
    }

    public function saveD($d)
    {
        $this->d = 'D:' . $d;
    }
}
