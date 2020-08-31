<?php

declare(strict_types=1);

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\DeserializationContext;

/**
 * Default Deserialization Context Factory.
 */
final class DefaultDeserializationContextFactory implements DeserializationContextFactoryInterface
{
    public function createDeserializationContext(): DeserializationContext
    {
        return new DeserializationContext();
    }
}
