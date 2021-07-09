<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class XmlElement
{
    /**
     * @var bool
     */
    public $cdata = true;

    /**
     * @var string|null
     */
    public $namespace = null;

    public function __construct(array $values = [], bool $cdata = true, ?string $namespace = null)
    {
        if (array_key_exists('cdata', $values)) {
            $cdata = $values['cdata'];
        }

        if (array_key_exists('namespace', $values)) {
            $namespace = $values['namespace'];
        }

        $this->cdata = $cdata;
        $this->namespace = $namespace;
    }
}
