<?php

declare(strict_types=1);

namespace JMS\Serializer\Accessor;

use JMS\Serializer\Context;
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
     * @param ClassMetadata $metadata
     * @param Context $context
     * @return PropertyMetadata[]
     */
    public function getProperties(ClassMetadata $metadata, Context $context): array;
    /**
     * @param object $object
     * @param PropertyMetadata[] $properties
     * @param SerializationContext $context
     * @return mixed[]
     */
    public function getValues(object $object, array $properties, SerializationContext $context):array;

    /**
     * @param object $object
     * @param mixed[] $values
     * @param PropertyMetadata[] $properties
     * @param DeserializationContext $context
     * @return void
     */
    public function setValues(object $object, array $values, array $properties, DeserializationContext $context): void;
}
