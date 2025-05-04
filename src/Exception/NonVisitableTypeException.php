<?php

declare(strict_types=1);

namespace JMS\Serializer\Exception;

use JMS\Serializer\Type\Type;

use function get_debug_type;

/**
 * @phpstan-import-type TypeArray from Type
 */
final class NonVisitableTypeException extends RuntimeException
{
    /**
     * @param mixed $data
     * @param TypeArray $type
     * @param RuntimeException|null $previous
     *
     * @return NonVisitableTypeException
     */
    public static function fromDataAndType($data, array $type, ?RuntimeException $previous = null): self
    {
        return new self(
            sprintf('Type %s cannot be visited as %s', get_debug_type($data), $type['name']),
            0,
            $previous,
        );
    }
}
