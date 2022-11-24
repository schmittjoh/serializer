<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;

class SkipWhenEmptyExclusionStrategy implements ValueExclusionStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function shouldSkipPropertyWithValue(PropertyMetadata $property, Context $context, $value): bool
    {
        if (
            $property->skipWhenEmpty
            || (
                $context->hasAttribute(Context::ATTR_SKIP_WHEN_EMPTY)
                && $context->getAttribute(Context::ATTR_SKIP_WHEN_EMPTY)
            )
        ) {
            if ($value instanceof \ArrayObject || \is_array($value) && 0 === count($value)) {
                return true;
            }

            // This would be used for T object types, later, in the visitor->visitProperty
            $property->skipWhenEmpty = true;
        }

        return false;
    }
}
