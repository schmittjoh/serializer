<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use function version_compare;

final class VersionExclusionStrategy implements ExclusionStrategyInterface
{
    private $version;

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext): bool
    {
        if ((null !== $version = $property->sinceVersion) && version_compare($this->version, $version, '<')) {
            return true;
        }

        if ((null !== $version = $property->untilVersion) && version_compare($this->version, $version, '>')) {
            return true;
        }

        return false;
    }
}
