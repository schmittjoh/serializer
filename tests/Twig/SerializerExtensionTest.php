<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Twig;

use JMS\Serializer\Twig\SerializerExtension;
use JMS\Serializer\Twig\SerializerRuntimeExtension;
use JMS\Serializer\Twig\SerializerRuntimeHelper;
use PHPUnit\Framework\TestCase;

class SerializerExtensionTest extends TestCase
{
    public function testSerialize()
    {
        $mockSerializer = $this->getMockBuilder('JMS\Serializer\SerializerInterface')->getMock();
        $obj = new \stdClass();
        $mockSerializer
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($obj), $this->equalTo('json'));
        $serializerExtension = new SerializerExtension($mockSerializer);
        $serializerExtension->serialize($obj);

        self::assertEquals('jms_serializer', $serializerExtension->getName());

        $filters = $serializerExtension->getFilters();
        self::assertInstanceOf('Twig_SimpleFilter', $filters[0]);
        self::assertSame([$serializerExtension, 'serialize'], $filters[0]->getCallable());

        self::assertEquals(
            [new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create')],
            $serializerExtension->getFunctions()
        );
    }

    public function testRuntimeSerializerHelper()
    {
        $obj = new \stdClass();

        $mockSerializer = $this->getMockBuilder('JMS\Serializer\SerializerInterface')->getMock();
        $mockSerializer
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($obj), $this->equalTo('json'));

        $serializerExtension = new SerializerRuntimeHelper($mockSerializer);
        $serializerExtension->serialize($obj);
    }

    public function testRuntimeSerializerExtension()
    {
        $serializerExtension = new SerializerRuntimeExtension();

        self::assertEquals('jms_serializer', $serializerExtension->getName());
        self::assertEquals(
            [new \Twig_SimpleFilter('serialize', [SerializerRuntimeHelper::class, 'serialize'])],
            $serializerExtension->getFilters()
        );
        self::assertEquals(
            [new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create')],
            $serializerExtension->getFunctions()
        );
    }
}
