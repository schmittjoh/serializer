<?php

namespace JMS\Serializer\Exception;

use Symfony\Component\Validator\ConstraintViolationList;

class ValidationFailedException extends RuntimeException
{
    private $list;

    public function __construct(ConstraintViolationList $list)
    {
        parent::__construct(sprintf('Validation failed with %d error(s).', count($list)));

        $this->list = $list;
    }

    public function getConstraintViolationList()
    {
        return $this->list;
    }
}