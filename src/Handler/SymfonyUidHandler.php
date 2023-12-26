<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;
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

final class SymfonyUidHandler implements SubscribingHandlerInterface
{
    public const FORMAT_BASE32    = 'base32';
    public const FORMAT_BASE58    = 'base58';
    public const FORMAT_CANONICAL = 'canonical';
    public const FORMAT_RFC4122   = 'rfc4122';

    private const UID_CLASSES = [
        Ulid::class,
        Uuid::class,
        UuidV1::class,
        UuidV3::class,
        UuidV4::class,
        UuidV5::class,
        UuidV6::class,
        UuidV7::class,
        UuidV8::class,
    ];

    /**
     * @var string
     * @phpstan-var self::FORMAT_*
     */
    private $defaultFormat;

    /**
     * @var bool
     */
    private $xmlCData;

    public function __construct(string $defaultFormat = self::FORMAT_CANONICAL, bool $xmlCData = true)
    {
        $this->defaultFormat = $defaultFormat;
        $this->xmlCData = $xmlCData;
    }

    public static function getSubscribingMethods(): array
    {
        $methods = [];
        $formats = ['json', 'xml'];

        foreach ($formats as $format) {
            foreach (self::UID_CLASSES as $class) {
                $methods[] = [
                    'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                    'format'    => $format,
                    'type'      => $class,
                    'method'    => 'serializeUid',
                ];

                $methods[] = [
                    'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                    'format'    => $format,
                    'type'      => $class,
                    'method'    => 'deserializeUidFrom' . ucfirst($format),
                ];
            }
        }

        return $methods;
    }

    /**
     * @phpstan-param array{name: class-string<AbstractUid>, params: array} $type
     */
    public function deserializeUidFromJson(DeserializationVisitorInterface $visitor, ?string $data, array $type, DeserializationContext $context): ?AbstractUid
    {
        if (null === $data) {
            return null;
        }

        return $this->deserializeUid($data, $type);
    }

    /**
     * @phpstan-param array{name: class-string<AbstractUid>, params: array} $type
     */
    public function deserializeUidFromXml(DeserializationVisitorInterface $visitor, \SimpleXMLElement $data, array $type, DeserializationContext $context): ?AbstractUid
    {
        if ($this->isDataXmlNull($data)) {
            return null;
        }

        return $this->deserializeUid((string) $data, $type);
    }

    /**
     * @phpstan-param array{name: class-string<AbstractUid>, params: array} $type
     */
    private function deserializeUid(string $data, array $type): ?AbstractUid
    {
        /** @var class-string<AbstractUid> $uidClass */
        $uidClass = $type['name'];

        try {
            return $uidClass::fromString($data);
        } catch (\InvalidArgumentException | \TypeError $exception) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid UID string.', $data), 0, $exception);
        }
    }

    /**
     * @return \DOMText|string
     *
     * @phpstan-param array{name: class-string<AbstractUid>, params: array} $type
     */
    public function serializeUid(SerializationVisitorInterface $visitor, AbstractUid $uid, array $type, SerializationContext $context)
    {
        /** @phpstan-var self::FORMAT_* $format */
        $format = $type['params'][0]['name'] ?? $this->defaultFormat;

        switch ($format) {
            case self::FORMAT_BASE32:
                $serialized = $uid->toBase32();
                break;

            case self::FORMAT_BASE58:
                $serialized = $uid->toBase58();
                break;

            case self::FORMAT_CANONICAL:
                $serialized = (string) $uid;
                break;

            case self::FORMAT_RFC4122:
                $serialized = $uid->toRfc4122();
                break;

            default:
                throw new InvalidArgumentException(sprintf('The "%s" format is not valid.', $format));
        }

        if ($visitor instanceof XmlSerializationVisitor && false === $this->xmlCData) {
            return $visitor->visitSimpleString($serialized, $type);
        }

        return $visitor->visitString($serialized, $type);
    }

    /**
     * @param mixed $data
     */
    private function isDataXmlNull($data): bool
    {
        $attributes = $data->attributes('xsi', true);

        return isset($attributes['nil'][0]) && 'true' === (string) $attributes['nil'][0];
    }
}
