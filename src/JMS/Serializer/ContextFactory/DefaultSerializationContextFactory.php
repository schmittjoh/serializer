<?php

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\SerializationContext;

/**
 * Default Serialization Context Factory.
 */
class DefaultSerializationContextFactory implements SerializationContextFactoryInterface
{
    /**
     * {@InheritDoc}
     */
    public function createSerializationContext()
    {
        return new SerializationContext();
    }
}
