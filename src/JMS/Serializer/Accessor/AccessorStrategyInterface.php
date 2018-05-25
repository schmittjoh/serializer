<?php

namespace JMS\Serializer\Accessor;

use JMS\Serializer\Metadata\PropertyMetadata;

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
    public function getValue($object, PropertyMetadata $metadata);

    /**
     * @param object $object
     * @param mixed $value
     * @param PropertyMetadata $metadata
     * @return void
     */
    public function setValue($object, $value, PropertyMetadata $metadata);
}
