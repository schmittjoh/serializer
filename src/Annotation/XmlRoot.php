<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class XmlRoot
{
    /**
     * @Required
     * @var string|null
     */
    public $name = null;

    /**
     * @var string|null
     */
    public $namespace = null;

    /**
     * @var string|null
     */
    public $prefix = null;

    public function __construct(array $values = [], ?string $name = null, ?string $namespace = null, ?string $prefix = null)
    {
        if (array_key_exists('value', $values)) {
            $name = $values['value'];
        }

        if (array_key_exists('name', $values)) {
            $name = $values['name'];
        }

        if (array_key_exists('namespace', $values)) {
            $namespace = $values['namespace'];
        }

        if (array_key_exists('prefix', $values)) {
            $prefix = $values['prefix'];
        }

        $this->name = $name;
        $this->namespace = $namespace;
        $this->prefix = $prefix;
    }
}
