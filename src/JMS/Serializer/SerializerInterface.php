<?php

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
     * @param object|array|scalar $data
     * @param string $format
     * @param Context $context
     *
     * @return string
     */
    public function serialize($data, $format, SerializationContext $context = null);

    /**
     * Deserializes the given data to the specified type.
     *
     * @param string $data
     * @param string $type
     * @param string $format
     * @param Context $context
     *
     * @return object|array|scalar
     */
    public function deserialize($data, $type, $format, DeserializationContext $context = null);
}
