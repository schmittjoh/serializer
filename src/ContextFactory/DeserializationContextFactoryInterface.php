<?php

declare(strict_types=1);

namespace JMS\Serializer\ContextFactory;

use JMS\Serializer\DeserializationContext;

/**
 * Deserialization Context Factory Interface.
 */
interface DeserializationContextFactoryInterface
{
    public function createDeserializationContext(): DeserializationContext;
}
