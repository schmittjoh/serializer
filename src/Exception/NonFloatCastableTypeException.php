<?php declare(strict_types=1);

namespace JMS\Serializer\Exception;

class NonFloatCastableTypeException extends NonCastableTypeException
{
    public function __construct($value)
    {
        parent::__construct('float', $value);
    }
}
