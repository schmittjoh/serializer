<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SymfonyUidHandler;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV1;
use Symfony\Component\Uid\UuidV3;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Uid\UuidV5;
use Symfony\Component\Uid\UuidV6;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Uid\UuidV8;

final class SymfonyUidHandlerTest extends TestCase
{
    public function dataUid(): \Generator
    {
        yield sprintf('%s instance', Ulid::class) => [new Ulid()];
        yield sprintf('%s instance', Uuid::class) => [Uuid::v1()];
        yield sprintf('%s instance', UuidV1::class) => [Uuid::v1()];
        yield sprintf('%s instance', UuidV3::class) => [Uuid::v3(Uuid::v4(), 'serializer-test')];
        yield sprintf('%s instance', UuidV4::class) => [Uuid::v4()];
        yield sprintf('%s instance', UuidV5::class) => [Uuid::v5(Uuid::v4(), 'serializer-test')];
        yield sprintf('%s instance', UuidV6::class) => [Uuid::v6()];

        if (class_exists(UuidV7::class)) {
            yield sprintf('%s instance', UuidV7::class) => [Uuid::v7()];
        }

        if (class_exists(UuidV8::class)) {
            yield sprintf('%s instance', UuidV8::class) => [Uuid::v8('216fff40-98d9-81e3-a5e2-0800200c9a66')];
        }
    }

    /**
     * @dataProvider dataUid
     */
    public function testSerializeUidToJson(AbstractUid $uid): void
    {
        self::assertJsonStringEqualsJsonString(
            sprintf('"%s"', (string) $uid),
            $this->createSerializer()->serialize($uid, 'json', null, AbstractUid::class)
        );
    }

    /**
     * @dataProvider dataUid
     */
    public function testSerializeUidToXmlWithCData(AbstractUid $uid): void
    {
        self::assertXmlStringEqualsXmlString(
            sprintf('<?xml version="1.0" encoding="UTF-8"?><result>%s</result>', (string) $uid),
            $this->createSerializer()->serialize($uid, 'xml', null, AbstractUid::class)
        );
    }

    /**
     * @dataProvider dataUid
     */
    public function testSerializeUidToXmlWithoutCData(AbstractUid $uid): void
    {
        self::assertXmlStringEqualsXmlString(
            sprintf('<?xml version="1.0" encoding="UTF-8"?><result>%s</result>', (string) $uid),
            $this->createSerializer(false)->serialize($uid, 'xml', null, AbstractUid::class)
        );
    }

    public function testSerializeUidToBase32(): void
    {
        $uid = Uuid::v4();

        self::assertJsonStringEqualsJsonString(
            sprintf('"%s"', $uid->toBase32()),
            $this->createSerializer()->serialize($uid, 'json', null, sprintf('%s<%s>', AbstractUid::class, SymfonyUidHandler::FORMAT_BASE32))
        );
    }

    public function testSerializeUidToBase58(): void
    {
        $uid = Uuid::v4();

        self::assertJsonStringEqualsJsonString(
            sprintf('"%s"', $uid->toBase58()),
            $this->createSerializer()->serialize($uid, 'json', null, sprintf('%s<%s>', AbstractUid::class, SymfonyUidHandler::FORMAT_BASE58))
        );
    }

    public function testSerializeUidToRfc4122(): void
    {
        $uid = Uuid::v4();

        self::assertJsonStringEqualsJsonString(
            sprintf('"%s"', $uid->toRfc4122()),
            $this->createSerializer()->serialize($uid, 'json', null, sprintf('%s<%s>', AbstractUid::class, SymfonyUidHandler::FORMAT_RFC4122))
        );
    }

    public function testSerializeUidRejectsInvalidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "unknown" format is not valid.');

        $this->createSerializer()->serialize(Uuid::v4(), 'json', null, sprintf('%s<unknown>', AbstractUid::class));
    }

    /**
     * @dataProvider dataUid
     */
    public function testDeserializeUidFromJson(AbstractUid $uid): void
    {
        self::assertTrue($uid->equals($this->createSerializer()->deserialize(sprintf('"%s"', (string) $uid), \get_class($uid), 'json')));
    }

    /**
     * @dataProvider dataUid
     */
    public function testDeserializeUidFromXml(AbstractUid $uid): void
    {
        self::assertTrue($uid->equals($this->createSerializer()->deserialize(sprintf('<?xml version="1.0" encoding="UTF-8"?><result>%s</result>', (string) $uid), \get_class($uid), 'xml')));
    }

    public function testDeserializeNullUidFromJson(): void
    {
        self::assertNull($this->createSerializer()->deserialize(json_encode(null), UuidV4::class, 'json'));
    }

    private function createSerializer(bool $xmlCData = true): SerializerInterface
    {
        $registry = new HandlerRegistry();
        $registry->registerSubscribingHandler(new SymfonyUidHandler(SymfonyUidHandler::FORMAT_CANONICAL, $xmlCData));

        return SerializerBuilder::create($registry, new EventDispatcher())->build();
    }
}
