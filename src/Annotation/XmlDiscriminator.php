<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class XmlDiscriminator
{
    /**
     * @var bool
     */
    public $attribute = false;

    /**
     * @var bool
     */
    public $cdata = true;

    /**
     * @var string|null
     */
    public $namespace = null;

    public function __construct(array $values = [], bool $attribute = false, bool $cdata = false, ?string $namespace = null)
    {
        if ([] !== $values) {
            if (array_key_exists('value', $values)) {
                $namespace = $values['value'];
            }

            if (array_key_exists('attribute', $values)) {
                $attribute = $values['attribute'];
            }

            if (array_key_exists('cdata', $values)) {
                $cdata = $values['cdata'];
            }

            if (array_key_exists('namespace', $values)) {
                $namespace = $values['namespace'];
            }
        }

        $this->attribute = $attribute;
        $this->cdata = $cdata;
        $this->namespace = $namespace;
    }
}
