<?php

namespace JMS\SerializerBundle\Annotation;

use JMS\SerializerBundle\Exception\RuntimeException;

class Type
{
    private $name;

    public function __construct(array $values)
    {
        if (!is_string($values['value'])) {
            throw new RuntimeException('"value" must be a string.');
        }

        $this->name = $values['value'];
    }

    public function getName()
    {
        return $this->name;
    }
}