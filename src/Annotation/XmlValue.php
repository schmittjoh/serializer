<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class XmlValue
{
    use AnnotationUtilsTrait;

    /**
     * @var bool
     */
    public $cdata = true;

    public function __construct(array $values = [], bool $cdata = true)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
