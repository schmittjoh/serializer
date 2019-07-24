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
    
    /**
     * Converts the given data to an array.
     *
     * @param mixed $data
     *
     * @return array
     */
    public function toArray($data, ?SerializationContext $context = null, ?string $type = null): array;
    
    /**
     * Converts the given array to the specified type.
     *
     * @param mixed $data
     */
    public function fromArray(array $data, string $type, ?DeserializationContext $context = null);
}
