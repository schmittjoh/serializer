<?php

namespace JMS\Serializer\Construction;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;

/**
 * Implementations of this interface construct new objects during deserialization.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface ObjectConstructorInterface
{
    /**
     * Constructs a new object.
     *
     * Implementations could for example create a new object calling "new", use
     * "unserialize" techniques, reflection, or other means.
     *
     * @param VisitorInterface $visitor
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param array $type ["name" => string, "params" => array]
     * @param DeserializationContext $context
     *
     * @return object
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context);
}
