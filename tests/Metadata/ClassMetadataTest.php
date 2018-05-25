<?php

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
        $meta = new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'b');
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
     */
    public function testAccessorTypePublicMethod($property, $getterInit, $setterInit, $getterName, $setterName)
    {
        $object = new PropertyMetadataPublicMethod();

        $metadata = new PropertyMetadata(get_class($object), $property);
        $metadata->setAccessor(PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD, $getterInit, $setterInit);

        $this->assertEquals($getterName, $metadata->getter);
        $this->assertEquals($setterName, $metadata->setter);

        $metadata->setValue($object, 'x');

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
