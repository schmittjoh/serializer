<?php declare(strict_types=1);

namespace JMS\Serializer\Exception;

class NonCastableTypeException extends RuntimeException
{
    /**
     * @var mixed
     */
    private $value;

    public function __construct($expectedType, $value)
    {
        $this->value = $value;

        parent::__construct(
            sprintf(
                'Cannot convert value of type "%s" to %s',
                gettype($value),
                $expectedType
            )
        );
    }

    public function getValue()
    {
        return $this->value;
    }
}
