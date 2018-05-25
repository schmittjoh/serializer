<?php

namespace JMS\Serializer;

/**
 * Interface for array transformation.
 *
 * @author Daniel Bojdo <daniel@bojdo.eu>
 */
interface ArrayTransformerInterface
{
    /**
     * Converts objects to an array structure.
     *
     * This is useful when the data needs to be passed on to other methods which expect array data.
     *
     * @param mixed $data anything that converts to an array, typically an object or an array of objects
     * @param SerializationContext|null $context
     *
     * @return array
     */
    public function toArray($data, SerializationContext $context = null);

    /**
     * Restores objects from an array structure.
     *
     * @param array $data
     * @param string $type
     * @param DeserializationContext|null $context
     *
     * @return mixed this returns whatever the passed type is, typically an object or an array of objects
     */
    public function fromArray(array $data, $type, DeserializationContext $context = null);
}
