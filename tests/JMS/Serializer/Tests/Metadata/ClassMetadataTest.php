<?php

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
}

class PropertyMetadataOrder
{
    private $b, $a;
}