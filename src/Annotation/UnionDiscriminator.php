<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class UnionDiscriminator implements SerializerAttribute
{
    use AnnotationUtilsTrait;

    /** @var string */
    public $field = 'type';

    public function __construct(array $values = [], string $field = 'type')
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
