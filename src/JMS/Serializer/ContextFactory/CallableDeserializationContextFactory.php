<?php

namespace JMS\Serializer\ContextFactory;

/**
 * Deserialization Context Factory using a callable.
 */
class CallableDeserializationContextFactory extends CallableContextFactory implements
    DeserializationContextFactoryInterface
{
    /**
     * {@InheritDoc}
     */
    public function createDeserializationContext()
    {
        return $this->createContext();
    }
}
