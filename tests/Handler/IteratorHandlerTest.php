<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\IteratorHandler;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use PHPUnit\Framework\TestCase;

final class IteratorHandlerTest extends TestCase
{
    public function testSerializeIterator(): void
    {
        $handler = new IteratorHandler();

        $visitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();
        $data = ['foo', 'bar'];
        $visitor->method('visitArray')->with($data)->willReturn($data);

        $context = $this->getMockBuilder(SerializationContext::class)->getMock();
        $type = ['name' => \Iterator::class, 'params' => []];

        $iterator = new \ArrayIterator($data);

        $results= $handler->serializeIterator($visitor, $iterator, $type, $context);
        self::assertCount(2, $results);
        self::assertIsArray($results);
    }


    public function testSerializeGenerator(): void
    {
        $handler = new IteratorHandler();

        $visitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();

        $data = ['foo', 'bar'];
        $visitor->method('visitArray')->with($data)->willReturn($data);

        $context = $this->getMockBuilder(SerializationContext::class)->getMock();
        $type = ['name' => \Generator::class, 'params' => []];

        $iterator = (static function (): \Generator {
            yield 'foo';
            yield 'bar';
        })();

        $results= $handler->serializeIterator($visitor, $iterator, $type, $context);
        self::assertCount(2, $results);
        self::assertIsArray($results);
    }

    public function testDeserializeIterator(): void
    {
        $handler = new IteratorHandler();

        $visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $data = ['foo', 'bar'];
        $visitor->method('visitArray')->with($data)->willReturn($data);

        $context = $this->getMockBuilder(DeserializationContext::class)->getMock();
        $type = ['name' => \Iterator::class, 'params' => []];

        $results = $handler->deserializeIterator($visitor, $data, $type, $context);
        self::assertCount(2, $results);
        self::assertInstanceOf(\Iterator::class, $results);
    }


    public function testDeserializeGenerator(): void
    {
        $handler = new IteratorHandler();

        $visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $data = ['foo', 'bar'];
        $visitor->method('visitArray')->with($data)->willReturn($data);

        $context = $this->getMockBuilder(DeserializationContext::class)->getMock();
        $type = ['name' => \Iterator::class, 'params' => []];

        $results = $handler->deserializeGenerator($visitor, $data, $type, $context);
        self::assertCount(2, $results);
        self::assertInstanceOf(\Generator::class, $results);
    }
}
