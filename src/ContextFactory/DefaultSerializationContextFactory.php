<?php

declare(strict_types=1);

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\SerializationContext;

/**
 * Default Serialization Context Factory.
 */
final class DefaultSerializationContextFactory implements SerializationContextFactoryInterface
{
    /**
     * {@InheritDoc}
     */
    public function createSerializationContext(): SerializationContext
    {
        return new SerializationContext();
    }
}
