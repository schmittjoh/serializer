<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use PHPUnit\Framework\TestCase;

class HandlerRegistryTest extends TestCase
{
    protected $handlerRegistry;

    protected function setUp(): void
    {
        $this->handlerRegistry = $this->createHandlerRegistry();
    }

    public function testRegisteredHandlersCanBeRetrieved()
    {
        $jsonSerializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'json', $jsonSerializationHandler);

        $jsonDeserializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'json', $jsonDeserializationHandler);

        $xmlSerializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'xml', $xmlSerializationHandler);

        $xmlDeserializationHandler = new DummyHandler();
        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'xml', $xmlDeserializationHandler);

        self::assertSame($jsonSerializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'json'));
        self::assertSame($jsonDeserializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'json'));
        self::assertSame($xmlSerializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, '\stdClass', 'xml'));
        self::assertSame($xmlDeserializationHandler, $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, '\stdClass', 'xml'));
    }

    /**
     * @dataProvider methodProvider
     */
    public function testCaseSensitiveDefaultMedhod(int $direction, string $type, string $format, string $result)
    {
        self::assertSame($result, HandlerRegistry::getDefaultMethod($direction, $type, $format));
    }

    public function methodProvider()
    {
        return [
            [GraphNavigatorInterface::DIRECTION_SERIALIZATION, 'DateTime', 'xml', 'serializeDateTimeToXml'],
            [GraphNavigatorInterface::DIRECTION_DESERIALIZATION, 'DateTime', 'xml', 'deserializeDateTimeFromXml'],
            [GraphNavigatorInterface::DIRECTION_SERIALIZATION, 'Foo\Bar', 'json', 'serializeBarToJson'],
            [GraphNavigatorInterface::DIRECTION_DESERIALIZATION, 'Foo\Bar', 'json', 'deserializeBarFromJson'],
        ];
    }

    protected function createHandlerRegistry()
    {
        return new HandlerRegistry();
    }
}

class DummyHandler
{
    public function __call($name, $arguments)
    {
    }
}
