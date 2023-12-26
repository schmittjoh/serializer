<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * Controls the order of properties in a class.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class AccessorOrder implements SerializerAttribute
{
    use AnnotationUtilsTrait;

    /**
     * @Required
     * @var string|null
     */
    public $order = null;

    /**
     * @var array<string>
     */
    public $custom = [];

    public function __construct(array $values = [], ?string $order = null, array $custom = [])
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
