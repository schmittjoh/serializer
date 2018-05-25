<?php

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\DeserializationContext;

/**
 * Deserialization Context Factory Interface.
 */
interface DeserializationContextFactoryInterface
{
    /**
     * @return DeserializationContext
     */
    public function createDeserializationContext();
}
