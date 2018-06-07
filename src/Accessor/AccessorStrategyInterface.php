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
     * @param object $object
     * @param PropertyMetadata $metadata
     * @return mixed
     */
    public function getValue(object $object, PropertyMetadata $metadata, SerializationContext $context);

    /**
     * @param object $object
     * @param mixed $value
     * @param PropertyMetadata $metadata
     * @return void
     */
    public function setValue(object $object, $value, PropertyMetadata $metadata, DeserializationContext $context): void;
}
