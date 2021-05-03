<?php

declare(strict_types=1);

namespace JMS\Serializer\Exception;

final class NonFloatCastableTypeException extends NonCastableTypeException
{
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct('float', $value);
    }
}
