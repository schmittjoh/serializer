<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
final class DepthExclusionStrategy implements ExclusionStrategyInterface
{
    private bool $cachedResult = false;
    private int $cachedStackCount = -1;
    private bool $hasMaxDepthOnStack = false;

    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool
    {
        return $this->isTooDeep($context);
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool
    {
        return $this->isTooDeep($context);
    }

    private function isTooDeep(Context $context): bool
    {
        $stack = $context->getMetadataStack();
        $currentCount = $stack->count();

        if ($currentCount === $this->cachedStackCount) {
            return $this->cachedResult;
        }

        if ($currentCount < $this->cachedStackCount && !$this->cachedResult) {
            $this->cachedStackCount = $currentCount;

            return false;
        }

        if (!$this->hasMaxDepthOnStack && !$this->cachedResult && $currentCount > $this->cachedStackCount) {
            $delta = $currentCount - $this->cachedStackCount;
            $found = false;
            $i = 0;
            foreach ($stack as $metadata) {
                if ($i >= $delta) {
                    break;
                }

                if ($metadata instanceof PropertyMetadata && null !== $metadata->maxDepth) {
                    $found = true;
                    break;
                }

                $i++;
            }

            if (!$found) {
                $this->cachedStackCount = $currentCount;

                return false;
            }
        }

        // Full scan
        $relativeDepth = 0;
        $top = $currentCount > 0 ? $stack->top() : null;
        $foundMaxDepth = false;

        foreach ($stack as $metadata) {
            if (!$metadata instanceof PropertyMetadata) {
                continue;
            }

            $relativeDepth++;

            if (null !== $metadata->maxDepth) {
                $foundMaxDepth = true;
            }

            if (0 === $metadata->maxDepth && $top === $metadata) {
                continue;
            }

            if (null !== $metadata->maxDepth && $relativeDepth > $metadata->maxDepth) {
                $this->cachedResult = true;
                $this->cachedStackCount = $currentCount;

                return true;
            }
        }

        $this->hasMaxDepthOnStack = $foundMaxDepth;
        $this->cachedResult = false;
        $this->cachedStackCount = $currentCount;

        return false;
    }
}
