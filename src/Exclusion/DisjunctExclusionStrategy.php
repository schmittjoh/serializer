<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Disjunct Exclusion Strategy.
 *
 * This strategy is short-circuiting and will skip a class, or property as soon as one of the delegates skips it.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class DisjunctExclusionStrategy implements ExclusionStrategyInterface
{
    /**
     * @var ExclusionStrategyInterface[]
     */
    private $delegates;

    /**
     * @param ExclusionStrategyInterface[] $delegates
     */
    public function __construct(array $delegates = [])
    {
        $this->delegates = $delegates;
    }

    public function addStrategy(ExclusionStrategyInterface $strategy): void
    {
        $this->delegates[] = $strategy;
    }

    /**
     * Whether the class should be skipped.
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool
    {
        foreach ($this->delegates as $delegate) {
            \assert($delegate instanceof ExclusionStrategyInterface);
            if ($delegate->shouldSkipClass($metadata, $context)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the property should be skipped.
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool
    {
        foreach ($this->delegates as $delegate) {
            \assert($delegate instanceof ExclusionStrategyInterface);
            if ($delegate->shouldSkipProperty($property, $context)) {
                return true;
            }
        }

        return false;
    }
}
