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

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAccessorOrder()
    {
        $metadata = new ClassMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder');
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'b'));
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'a'));
        $this->assertEquals(array('b', 'a'), array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_ALPHABETICAL);
        $this->assertEquals(array('a', 'b'), array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_CUSTOM, array('b', 'a'));
        $this->assertEquals(array('b', 'a'), array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_CUSTOM, array('a', 'b'));
        $this->assertEquals(array('a', 'b'), array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_CUSTOM, array('b'));
        $this->assertEquals(array('b', 'a'), array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_CUSTOM, array('a'));
        $this->assertEquals(array('a', 'b'), array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_CUSTOM, array('foo', 'bar'));
        $this->assertEquals(array('b', 'a'), array_keys($metadata->propertyMetadata));
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
