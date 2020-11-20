<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

class SkipWhenEmptyExclusionStrategy implements ExclusionStrategyInterface
{
    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool
    {
        return false;
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool
    {
        if (!$property->skipWhenEmpty
            && $context->hasAttribute(Context::ATTR_SKIP_WHEN_EMPTY)
                && $context->getAttribute(Context::ATTR_SKIP_WHEN_EMPTY)
            ) {
            $property->skipWhenEmpty = true;
        }
        return false;
    }
}
