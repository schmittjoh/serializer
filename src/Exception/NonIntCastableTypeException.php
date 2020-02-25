<?php declare(strict_types=1);

namespace JMS\Serializer\Exception;

class NonIntCastableTypeException extends NonCastableTypeException
{
    public function __construct($value)
    {
        parent::__construct('int', $value);
    }
}
