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
     * @param string $format
     * @param SerializationContext $context
     * @param string $type
     * @return string
     */
    public function serialize($data, string $format, SerializationContext $context = null, string $type = null): string;

    /**
     * Deserializes the given data to the specified type.
     *
     * @param string $data
     * @param string $type
     * @param string $format
     * @param DeserializationContext $context
     *
     * @return mixed
     */
    public function deserialize(string $data, string $type, string $format, DeserializationContext $context = null);
}
