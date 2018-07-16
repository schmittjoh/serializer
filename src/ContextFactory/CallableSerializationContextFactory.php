<?php

declare(strict_types=1);

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\SerializationContext;

/**
 * Serialization Context Factory using a callable.
 */
final class CallableSerializationContextFactory extends CallableContextFactory implements
    SerializationContextFactoryInterface
{
    /**
     * {@InheritDoc}
     */
    public function createSerializationContext(): SerializationContext
    {
        return $this->createContext();
    }
}
