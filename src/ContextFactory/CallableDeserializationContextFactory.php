<?php

declare(strict_types=1);

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\DeserializationContext;

/**
 * Deserialization Context Factory using a callable.
 */
final class CallableDeserializationContextFactory extends CallableContextFactory implements
    DeserializationContextFactoryInterface
{
    public function createDeserializationContext(): DeserializationContext
    {
        return $this->createContext();
    }
}
