<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class XmlAttribute
{
    /**
     * @var string|null
     */
    public $namespace = null;

    public function __construct(array $values = [], ?string $namespace = null)
    {
        if ([] !== $values) {
            if (array_key_exists('value', $values)) {
                $namespace = $values['value'];
            }

            if (array_key_exists('namespace', $values)) {
                $namespace = $values['namespace'];
            }
        }

        $this->namespace = $namespace;
    }
}
