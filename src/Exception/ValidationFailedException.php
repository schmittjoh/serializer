<?php

declare(strict_types=1);

namespace JMS\Serializer\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use function count;
use function sprintf;

class ValidationFailedException extends RuntimeException
{
    /** @var ConstraintViolationListInterface */
    private $list;

    public function __construct(ConstraintViolationListInterface $list)
    {
        parent::__construct(sprintf('Validation failed with %d error(s).', count($list)));

        $this->list = $list;
    }

    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->list;
    }
}
