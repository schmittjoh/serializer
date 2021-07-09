<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class Expose
{
    /**
     * @var string|null
     */
    public $if = null;

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
