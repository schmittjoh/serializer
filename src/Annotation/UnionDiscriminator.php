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

    /** @var array<string> */
    public $map = [];

    /** @var string */
    public $field = 'type';

    public function __construct(array $values = [], string $field = 'type', array $map = [])
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
