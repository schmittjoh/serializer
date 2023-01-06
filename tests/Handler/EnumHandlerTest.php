<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Handler\EnumHandler;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Tests\Fixtures\Enum\BackedSuitInt;
use JMS\Serializer\Tests\Fixtures\Enum\Suit;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use PHPUnit\Framework\TestCase;

final class EnumHandlerTest extends TestCase
{
    /**
     * @var EnumHandler
     */
    private $hander;

    protected function setUp(): void
    {
        if (PHP_VERSION_ID < 80100) {
            self::markTestSkipped('No ENUM support');
        }

        $this->hander = new EnumHandler();
    }

    public function testOrdinaryEnumCanNotBeUsedAsBackedEnumWhenSerializing()
    {
        self::expectException(InvalidMetadataException::class);
        self::expectExceptionMessage(sprintf('The type "%s" is not a backed enum, thus you can not use "value" as serialization mode for its value.', Suit::class));

        $visitor = $this->createMock(SerializationVisitorInterface::class);
        $context = $this->createMock(SerializationContext::class);
        $type = [
            'name' => 'enum',
            'params' => [Suit::class, 'value'],
        ];
        $this->hander->serializeEnum($visitor, Suit::Clubs, $type, $context);
    }

    public function testOrdinaryEnumCanNotBeUsedAsBackedEnumWhenDeserializing()
    {
        self::expectException(InvalidMetadataException::class);
        self::expectExceptionMessage(sprintf('The type "%s" is not a backed enum, thus you can not use "value" as serialization mode for its value.', Suit::class));

        $visitor = $this->createMock(DeserializationVisitorInterface::class);
        $type = [
            'name' => 'enum',
            'params' => [Suit::class, 'value'],
        ];
        $this->hander->deserializeEnum($visitor, 'any', $type);
    }

    public function testBackedDeserializationFailsWhenNoValueMatches()
    {
        self::expectError();

        $visitor = $this->createMock(DeserializationVisitorInterface::class);
        $type = [
            'name' => 'enum',
            'params' => [BackedSuitInt::class, 'value'],
        ];
        $this->hander->deserializeEnum($visitor, 7, $type);
    }

    public function testBackedDeserializationFailsWhenValueTypeMismatch()
    {
        self::expectException(RuntimeException::class);
        self::expectErrorMessage(sprintf('"any" is not a valid backing value for enum "%s"', BackedSuitInt::class));

        $visitor = $this->createMock(DeserializationVisitorInterface::class);
        $type = [
            'name' => 'enum',
            'params' => [BackedSuitInt::class, 'value'],
        ];
        $this->hander->deserializeEnum($visitor, 'any', $type);
    }

    public function testDeserializeBacked()
    {
        $visitor = $this->createMock(DeserializationVisitorInterface::class);
        $type = [
            'name' => 'enum',
            'params' => [BackedSuitInt::class, 'value'],
        ];
        $enum = $this->hander->deserializeEnum($visitor, 4, $type);

        self::assertSame(BackedSuitInt::Spades, $enum);
    }

    public function testDeserializeBackedAuto()
    {
        $visitor = $this->createMock(DeserializationVisitorInterface::class);
        $type = [
            'name' => 'enum',
            'params' => [BackedSuitInt::class],
        ];
        $enum = $this->hander->deserializeEnum($visitor, 4, $type);

        self::assertSame(BackedSuitInt::Spades, $enum);
    }

    public function testDeserializeBackedAsOrdinary()
    {
        $visitor = $this->createMock(DeserializationVisitorInterface::class);
        $type = [
            'name' => 'enum',
            'params' => [BackedSuitInt::class, 'name'],
        ];
        $enum = $this->hander->deserializeEnum($visitor, 'Spades', $type);

        self::assertSame(BackedSuitInt::Spades, $enum);
    }

    public function testDeserializeOrdinary()
    {
        $visitor = $this->createMock(DeserializationVisitorInterface::class);
        $type = [
            'name' => 'enum',
            'params' => [Suit::class, 'name'],
        ];
        $enum = $this->hander->deserializeEnum($visitor, 'Spades', $type);

        self::assertSame(Suit::Spades, $enum);
    }

    public function testDeserializeOrdinaryAuto()
    {
        $visitor = $this->createMock(DeserializationVisitorInterface::class);
        $type = [
            'name' => 'enum',
            'params' => [Suit::class],
        ];
        $enum = $this->hander->deserializeEnum($visitor, 'Spades', $type);

        self::assertSame(Suit::Spades, $enum);
    }
}
