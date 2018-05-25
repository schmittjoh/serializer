<?php

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\DeserializationContext;

/**
 * Default Deserialization Context Factory.
 */
class DefaultDeserializationContextFactory implements DeserializationContextFactoryInterface
{
    /**
     * {@InheritDoc}
     */
    public function createDeserializationContext()
    {
        return new DeserializationContext();
    }
}
