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
        $depth = $context->getDepth();
        $metadataStack = $context->getMetadataStack();

        $nthProperty = 0;
        // iterate from the first added items to the lasts
        for ($i = $metadataStack->count() - 1; $i > 0; $i--) {
            $metadata = $metadataStack[$i];
            if ($metadata instanceof PropertyMetadata) {
                $nthProperty++;
                $relativeDepth = $depth - $nthProperty;

                if (null !== $metadata->maxDepth && $relativeDepth > $metadata->maxDepth) {
                    return true;
                }
            }
        }

        return false;
    }
}
