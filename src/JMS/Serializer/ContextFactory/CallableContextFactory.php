<?php

namespace JMS\Serializer\ContextFactory;

/**
 * Context Factory using a callable.
 */
abstract class CallableContextFactory
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @return mixed
     */
    protected function createContext()
    {
        $callable = $this->callable;

        return $callable();
    }
}
