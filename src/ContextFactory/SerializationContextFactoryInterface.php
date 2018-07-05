<?php

declare(strict_types=1);

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\SerializationContext;

/**
 * Serialization Context Factory Interface.
 */
interface SerializationContextFactoryInterface
{
    public function createSerializationContext(): SerializationContext;
}
