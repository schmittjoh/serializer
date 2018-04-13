<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
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

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

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

    public function testSerialization()
    {
        $meta = new PropertyMetadata(PropertyMetadataOrder::class, 'b');
        $restoredMeta = unserialize(serialize($meta));
        $this->assertEquals($meta, $restoredMeta);
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
     * @param string $property
     * @param string|null $type
     * @param string|null $getter
     * @param string|null $setter
     */
    public function testAccessorTypePublicMethod($property, $type, $getter, $setter)
    {
        $object = new PropertyMetadataPublicMethod();

        $metadata = new PropertyMetadata(get_class($object), $property);
        $metadata->setAccessor($type, $getter, $setter);

        $this->assertEquals($getter, $metadata->getter);
        $this->assertEquals($setter, $metadata->setter);
        $this->assertEquals($type, $metadata->accessType);

        $metadata->setValue($object, 'x');

        $this->assertEquals(
            sprintf('%1$s:%1$s:x', strtoupper($property)),
            $metadata->getValue($object)
        );
    }

    public function providerPublicMethodData()
    {
        return [
            ['a', PropertyMetadata::ACCESS_TYPE_PROPERTY, 'geta', 'seta'],
            ['b', PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD, 'isb', 'setb'],
            ['c', PropertyMetadata::ACCESS_TYPE_PROPERTY, 'hasc', 'setc'],
            ['d', PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD, 'fetchd', 'saved']
        ];
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
