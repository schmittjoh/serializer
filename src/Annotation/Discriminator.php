<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Discriminator implements SerializerAttribute
{
    use AnnotationUtilsTrait;

    /** @var array<string> */
    public $map = [];

    /** @var string */
    public $field = 'type';

    /** @var bool */
    public $disabled = false;

    /** @var bool */
    public $virtual = true;

    /** @var string[] */
    public $groups = [];

    /** @var string */
    public $default;

    public function __construct(array $values = [], string $field = 'type', array $groups = [], array $map = [], bool $disabled = false, ?string $default = null, bool $virtual = true)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
