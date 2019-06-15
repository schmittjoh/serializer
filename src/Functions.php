<?php

declare(strict_types=1);

namespace JMS\Serializer;

use function iterator_to_array;

final class Functions
{
    /**
     * Copy the iterable into an array. If the iterable is already an array, return it.
     *
     * @return mixed[]
     */
    public static function iterableToArray(iterable $iterable): array
    {
        return is_array($iterable) ? $iterable : iterator_to_array($iterable);
    }
}
