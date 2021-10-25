<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\RuntimeException;

/**
 * Serializer Interface.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface SerializerInterface
{
    /**
     * Serializes the given data to the specified output format.
     *
     * @param mixed $data
     *
     * @throws RuntimeException
     */
    public function serialize($data, string $format, ?SerializationContext $context = null, ?string $type = null): string;

    /**
     * Deserializes the given data to the specified type.
     *
     * @return mixed
     *
     * @throws RuntimeException
     *
     * @psalm-template T
     * @psalm-param class-string<T>|mixed $type // mixed, because "array<Foo>" doesn't match class-string<T>
     * @psalm-return T
     */
    public function deserialize(string $data, string $type, string $format, ?DeserializationContext $context = null);
}
