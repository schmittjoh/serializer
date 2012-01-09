<?php

namespace JMS\SerializerBundle\Tests\Metadata;

use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\ClassMetadata;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAccessorOrder()
    {
        $metadata = new ClassMetadata('JMS\SerializerBundle\Tests\Metadata\PropertyMetadataOrder');
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\SerializerBundle\Tests\Metadata\PropertyMetadataOrder', 'b'));
        $metadata->addPropertyMetadata(new PropertyMetadata('JMS\SerializerBundle\Tests\Metadata\PropertyMetadataOrder', 'a'));
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