<?php

declare(strict_types=1);

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\DeserializationContext;

final class CallableDeserializationContextFactory implements
    DeserializationContextFactoryInterface
{
    /**
     * @var callable():DeserializationContext
     */
    private $callable;

    /**
     * @param callable():DeserializationContext $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function createDeserializationContext(): DeserializationContext
    {
        $callable = $this->callable;

        return $callable();
    }
}
