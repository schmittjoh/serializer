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
     */
    public function deserialize(string $data, string $type, string $format, ?DeserializationContext $context = null);
}
