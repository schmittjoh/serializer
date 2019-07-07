<?php

declare(strict_types=1);

namespace JMS\Serializer;

/**
 * Serializer Interface.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface SerializerInterface
{
    public const FORMAT_JSON = 'json';
    public const FORMAT_XML = 'xml';

    public const FORMATS_SUPPORTED = [
        self::FORMAT_JSON,
        self::FORMAT_XML,
    ];

    /**
     * Serializes the given data to the specified output format.
     *
     * @param mixed $data
     */
    public function serialize($data, string $format, ?SerializationContext $context = null, ?string $type = null): string;

    /**
     * Deserializes the given data to the specified type.
     *
     * @return mixed
     *
     * @psalm-template T
     * @psalm-param class-string<T> $type
     * @psalm-return T
     */
    public function deserialize(string $data, string $type, string $format, ?DeserializationContext $context = null);
}
