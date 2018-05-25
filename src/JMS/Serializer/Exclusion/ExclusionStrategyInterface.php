<?php

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Interface for exclusion strategies.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface ExclusionStrategyInterface
{
    /**
     * Whether the class should be skipped.
     *
     * @param ClassMetadata $metadata
     *
     * @return boolean
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $context);

    /**
     * Whether the property should be skipped.
     *
     * @param PropertyMetadata $property
     *
     * @return boolean
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $context);
}
