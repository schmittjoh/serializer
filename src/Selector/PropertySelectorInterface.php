<?php

declare(strict_types=1);

namespace JMS\Serializer\Selector;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface PropertySelectorInterface
{
    /**
     * @param ClassMetadata $metadata
     * @param Context $context
     * @return PropertyMetadata[]
     */
    public function select(ClassMetadata $metadata, Context $context): array;
}
