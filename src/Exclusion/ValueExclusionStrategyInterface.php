<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Interface for exclusion strategies including the values
 *
 * @author Veaceslav vasilache <slava.dev@gmail.com>
 */
interface ValueExclusionStrategyInterface
{
    /**
     * Whether the property should be skipped, using the value also
     *
     * @param PropertyMetadata $property
     * @param Context $context
     * @param mixed $value
     *
     * @return bool
     */
    public function shouldSkipPropertyWithValue(PropertyMetadata $property, Context $context, $value): bool;
}
