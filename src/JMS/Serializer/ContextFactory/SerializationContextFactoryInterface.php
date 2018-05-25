<?php

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\SerializationContext;

/**
 * Serialization Context Factory Interface.
 */
interface SerializationContextFactoryInterface
{
    /**
     * @return SerializationContext
     */
    public function createSerializationContext();
}
