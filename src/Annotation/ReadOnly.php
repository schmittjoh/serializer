<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"CLASS","PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
final class ReadOnly
{
    /**
     * @var bool
     */
    public $readOnly = true;

    public function __construct(array $values = [], bool $readOnly = true)
    {
        if ([] !== $values) {
            if (array_key_exists('value', $values)) {
                $readOnly = $values['value'];
            }

            if (array_key_exists('readOnly', $values)) {
                $readOnly = $values['readOnly'];
            }
        }

        $this->readOnly =  $readOnly;
    }
}
