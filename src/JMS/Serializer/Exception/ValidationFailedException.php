<?php

namespace JMS\Serializer\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationFailedException extends RuntimeException
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $list;

    public function __construct(ConstraintViolationListInterface $list)
    {
        parent::__construct(sprintf('Validation failed with %d error(s).', \count($list)));

        $this->list = $list;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getConstraintViolationList()
    {
        return $this->list;
    }
}
