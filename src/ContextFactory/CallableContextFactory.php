<?php

declare(strict_types=1);

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\Context;

/**
 * Context Factory using a callable.
 */
abstract class CallableContextFactory
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @return mixed
     */
    protected function createContext(): Context
    {
        $callable = $this->callable;

        return $callable();
    }
}
