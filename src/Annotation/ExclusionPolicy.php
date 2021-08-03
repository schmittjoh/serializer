<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

use JMS\Serializer\Exception\RuntimeException;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ExclusionPolicy
{
    use AnnotationUtilsTrait;

    public const NONE = 'NONE';
    public const ALL = 'ALL';

    /**
     * @var string|null
     */
    public $policy = 'NONE';

    public function __construct($values = [], ?string $policy = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());

        $this->policy = strtoupper($this->policy);

        if (self::NONE !== $this->policy && self::ALL !== $this->policy) {
            throw new RuntimeException('Exclusion policy must either be "ALL", or "NONE".');
        }
    }
}
