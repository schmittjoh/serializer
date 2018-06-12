<?php

declare(strict_types=1);

namespace JMS\Serializer\Accessor;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface AccessorStrategyInterface
{
    /**
     * @param object $object
     * @param ClassMetadata $metadata
     * @param PropertyMetadata[] $properties
     * @param SerializationContext $context
     * @return mixed[]
     */
    public function getValues(object $object,  ClassMetadata $metadata, array $properties, SerializationContext $context):array;

    /**
     * @param object $object
     * @param mixed[] $values
     * @param ClassMetadata $metadata
     * @param PropertyMetadata[] $properties
     * @param DeserializationContext $context
     * @return void
     */
    public function setValues(object $object, array $values,  ClassMetadata $metadata, array $properties, DeserializationContext $context): void;
}
