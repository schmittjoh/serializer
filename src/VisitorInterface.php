<?php

declare(strict_types=1);

namespace JMS\Serializer;

/**
 * Interface for visitors.
 *
 * This contains the minimal set of values that must be supported for any
 * output format.
 *
 * @internal
 */
interface VisitorInterface
{
    /**
     * Allows visitors to convert the input data to a different representation
     * before the actual serialization/deserialization process starts.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function prepare($data);

    /**
     * Called before serialization/deserialization starts.
     *
     *
     */
    public function setNavigator(GraphNavigatorInterface $navigator): void;

    /**
     * Get the result of the serialization/deserialization process.
     *
     * @param mixed $data
     * @return mixed
     */
    public function getResult($data);
}
