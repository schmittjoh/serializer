<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class XmlAttribute implements SerializerAttribute
{
    use AnnotationUtilsTrait;

    /**
     * @var string|null
     */
    public $namespace = null;

    public function __construct($values = [], ?string $namespace = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
