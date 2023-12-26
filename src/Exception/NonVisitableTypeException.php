<?php

declare(strict_types=1);

namespace JMS\Serializer\Exception;

use function get_debug_type;

final class NonVisitableTypeException extends RuntimeException
{
    /**
     * @param mixed $data
     * @param array{name: string} $type
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
