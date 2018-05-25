<?php

namespace JMS\Serializer\Naming;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Interface for advanced property naming strategies.
 *
 * Implementations translate the property name to a serialized name that is
 * displayed. It allows advanced strategy thanks to context parameter.
 *
 * @author Vincent Rasquier <vincent.rsbs@gmail.com>
 */
interface AdvancedNamingStrategyInterface
{
    /**
     * Translates the name of the property to the serialized version.
     *
     * @param PropertyMetadata $property
     * @param Context $context
     *
     * @return string
     */
    public function getPropertyName(PropertyMetadata $property, Context $context);
}
