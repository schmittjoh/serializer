<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\ExclusionStrategy;

use JMS\Serializer\Context;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

class AlwaysExcludeExclusionStrategy implements ExclusionStrategyInterface
{
    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool
    {
        return true;
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool
    {
        return false;
    }
}
