<?php

namespace JMS\Serializer\ContextFactory;

/**
 * Serialization Context Factory using a callable.
 */
class CallableSerializationContextFactory extends CallableContextFactory implements
    SerializationContextFactoryInterface
{
    /**
     * {@InheritDoc}
     */
    public function createSerializationContext()
    {
        return $this->createContext();
    }
}
