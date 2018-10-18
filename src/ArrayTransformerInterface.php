<?php

declare(strict_types=1);

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
     *
     * @return array
     */
    public function toArray($data, ?SerializationContext $context = null, ?string $type = null): array;

    /**
     * Restores objects from an array structure.
     *
     * @param array $data
     *
     * @return mixed this returns whatever the passed type is, typically an object or an array of objects
     */
    public function fromArray(array $data, string $type, ?DeserializationContext $context = null);
}
