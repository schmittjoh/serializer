<?php

namespace JMS\SerializerBundle\Tests\Serializer\Normalizer;

use JMS\SerializerBundle\Serializer\Normalizer\ArrayCollectionNormalizer;

class ArrayCollectionNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testDenormalize()
    {
        return;
        $data = array(
            'foo' => 'bar',
        );

        $serializer = $this->getMockBuilder('JMS\SerializerBundle\Serializer\Serializer')
                        ->setMethods(array('denormalize'))
                        ->disableOriginalConstructor()
                        ->getMock();
        $serializer
            ->expects($this->once())
            ->method('denormalize')
            ->with($this->equalTo($data), $this->equalTo('array<Foo>'))
            ->will($this->returnValue(array('foo' => $obj = new \stdClass)))
        ;

        $normalizer = new ArrayCollectionNormalizer();
        $normalizer->setSerializer($serializer);

        $denormalized = $normalizer->denormalize($data, 'ArrayCollection<Foo>');
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $denormalized);
        $this->assertSame($obj, $denormalized['foo']);
    }

    public function testSupportsDenormalization()
    {
        $normalizer = new ArrayCollectionNormalizer();
        $this->assertTrue($normalizer->supportsDenormalization(null, 'ArrayCollection<>'));
        $this->assertFalse($normalizer->supportsDenormalization(null, 'ArrayCollection'));
    }
}