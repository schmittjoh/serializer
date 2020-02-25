<?php declare(strict_types=1);

namespace JMS\Serializer\Exception;

class NonStringCastableTypeException extends NonCastableTypeException
{
    public function __construct($value)
    {
        parent::__construct('string', $value);
    }
}
