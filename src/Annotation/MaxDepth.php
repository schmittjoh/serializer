<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class MaxDepth
{
    /**
     * @Required
     * @var int
     */
    public $depth;

    public function __construct(array $values = [], int $depth = 0)
    {
        if ([] !== $values) {
            if (array_key_exists('value', $values)) {
                $depth = $values['value'];
            }

            if (array_key_exists('depth', $values)) {
                $depth = $values['depth'];
            }
        }

        $this->depth = $depth;
    }
}
