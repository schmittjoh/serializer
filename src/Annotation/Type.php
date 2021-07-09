<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class Type
{
    /**
     * @Required
     * @var string|null
     */
    public $name = null;

    public function __construct(array $values = [], ?string $name = null)
    {
        if (array_key_exists('value', $values)) {
            $name = $values['value'];
        }

        if (array_key_exists('name', $values)) {
            $name = $values['name'];
        }

        $this->name = $name;
    }
}
