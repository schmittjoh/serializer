<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
final class AccessType
{
    use AnnotationUtilsTrait;

    /**
     * @Required
     * @var string|null
     */
    public $type;

    public function __construct(array $values = [], ?string $type = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
