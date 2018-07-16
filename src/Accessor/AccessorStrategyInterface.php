<?php

declare(strict_types=1);

namespace JMS\Serializer\Accessor;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface AccessorStrategyInterface
{
    /**
     * @return mixed
     */
    public function getValue(object $object, PropertyMetadata $metadata, SerializationContext $context);

    /**
     * @param mixed $value
     */
    public function setValue(object $object, $value, PropertyMetadata $metadata, DeserializationContext $context): void;
}
