<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "CLASS", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class Exclude
{
    /**
     * @var string|null
     */
    public $if;

    public function __construct(array $values = [], ?string $if = null)
    {
        if (array_key_exists('value', $values)) {
            $if = $values['value'];
        }

        if (array_key_exists('if', $values)) {
            $if = $values['if'];
        }

        $this->if = $if;
    }
}
