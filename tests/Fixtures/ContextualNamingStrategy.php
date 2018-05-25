<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\AdvancedNamingStrategyInterface;

/**
 * Class ContextualNamingStrategy
 *
 * Only use this class for testing purpose
 */
class ContextualNamingStrategy implements AdvancedNamingStrategyInterface
{
    public function getPropertyName(PropertyMetadata $property, Context $context)
    {
        if ($context->getDirection() == GraphNavigator::DIRECTION_SERIALIZATION) {
            return strtoupper($property->name);
        }
        return ucfirst($property->name);
    }
}
