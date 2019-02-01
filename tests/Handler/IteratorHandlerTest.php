<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\IteratorHandler;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use PHPUnit\Framework\TestCase;

final class IteratorHandlerTest extends TestCase
{
    private const DATA = ['foo', 'bar'];

    /**
     * @var HandlerRegistry
     */
    private $handlerRegistry;

    public function iteratorsProvider(): array
    {
        return [
            [
                new \ArrayIterator(self::DATA),
            ],
            [
                (static function (): \Generator {
                    yield 'foo';
                    yield 'bar';
                })(),
            ],
        ];
    }

    /**
     * @dataProvider iteratorsProvider
     */
    public function testSerialize(\Iterator $iterator): void
    {
        $type = get_class($iterator);
        $serializationHandler = $this->handlerRegistry->getHandler(
            GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            $type,
            'json'
        );
        self::assertIsCallable($serializationHandler);

        $serialized = $serializationHandler(
            $this->createSerializationVisitor(),
            $iterator,
            ['name' => $type, 'params' => []],
            $this->getMockBuilder(SerializationContext::class)->getMock()
        );
        self::assertSame(self::DATA, $serialized);

        $deserializationHandler = $this->handlerRegistry->getHandler(
            GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
            $type,
            'json'
        );
        self::assertIsCallable($deserializationHandler);

        $deserialized = $deserializationHandler(
            $this->createDeserializationVisitor(),
            $serialized,
            ['name' => $type, 'params' => []],
            $this->getMockBuilder(DeserializationContext::class)->getMock()
        );
        self::assertEquals($iterator, $deserialized);
    }

    protected function setUp(): void
    {
        $this->handlerRegistry = new HandlerRegistry();
        $this->handlerRegistry->registerSubscribingHandler(new IteratorHandler());
    }

    private function createDeserializationVisitor(): DeserializationVisitorInterface
    {
        $visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $visitor->method('visitArray')->with(self::DATA)->willReturn(self::DATA);
        return $visitor;
    }

    private function createSerializationVisitor(): SerializationVisitorInterface
    {
        $visitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();
        $visitor->method('visitArray')->with(self::DATA)->willReturn(self::DATA);
        return $visitor;
    }
}
