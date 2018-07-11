<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use PHPUnit\Framework\TestCase;

class ClassMetadataTest extends TestCase
{
    public function getAccessOrderCases()
    {
        return [
            [['b', 'a'], ['b', 'a']],
            [['a', 'b'], ['a', 'b']],
            [['b'], ['b', 'a']],
            [['a'], ['a', 'b']],
            [['foo', 'bar'], ['b', 'a']],
        ];
    }

    public function testSerialization()
    {
        $meta = new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'b');
        $restoredMeta = unserialize(serialize($meta));
        self::assertEquals($meta, $restoredMeta);
    }

    /**
     * @dataProvider getAccessOrderCases
     */
    public function testSetAccessorOrderCustom(array $order, array $expected)
    {
        $metadata = new ClassMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder');
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'b'));
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'a'));
        self::assertEquals(['b', 'a'], array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_CUSTOM, $order);
        self::assertEquals($expected, array_keys($metadata->propertyMetadata));
    }

    public function testSetAccessorOrderAlphabetical()
    {
        $metadata = new ClassMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder');
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'b'));
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\Serializer\Tests\Metadata\PropertyMetadataOrder', 'a'));
        self::assertEquals(['b', 'a'], array_keys($metadata->propertyMetadata));

        $metadata->setAccessorOrder(ClassMetadata::ACCESSOR_ORDER_ALPHABETICAL);
        self::assertEquals(['a', 'b'], array_keys($metadata->propertyMetadata));
    }

    /**
     * @dataProvider providerPublicMethodData
     */
    public function testAccessorTypePublicMethod($property, $getterInit, $setterInit, $getterName, $setterName)
    {
        $object = new PropertyMetadataPublicMethod();

        $metadata = new PropertyMetadata(get_class($object), $property);
        $metadata->setAccessor(PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD, $getterInit, $setterInit);

        self::assertEquals($getterName, $metadata->getter);
        self::assertEquals($setterName, $metadata->setter);
    }

    /**
     * @dataProvider providerPublicMethodException
     */
    public function testAccessorTypePublicMethodException($getter, $setter, $message)
    {
        $this->expectException('\JMS\Serializer\Exception\InvalidMetadataException');
        $this->expectExceptionMessage($message);

        $object = new PropertyMetadataPublicMethod();

        $metadata = new PropertyMetadata(get_class($object), 'e');
        $metadata->setAccessor(PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD, $getter, $setter);
    }

    public function providerPublicMethodData()
    {
        return [
            ['a', null, null, 'geta', 'seta'],
            ['b', null, null, 'isb', 'setb'],
            ['c', null, null, 'hasc', 'setc'],
            ['d', 'fetchd', 'saved', 'fetchd', 'saved'],
        ];
    }

    public function providerPublicMethodException()
    {
        return [
            [null, null, 'a public getE method, nor a public isE method, nor a public hasE method in class'],
            [null, 'setx', 'a public getE method, nor a public isE method, nor a public hasE method in class'],
            ['getx', null, 'no public setE method in class'],
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
