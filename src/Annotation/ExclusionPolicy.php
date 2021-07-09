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
    public const NONE = 'NONE';
    public const ALL = 'ALL';

    /**
     * @var string|null
     */
    public $policy = null;

    public function __construct(array $values = [], ?string $policy = null)
    {
        $value = self::NONE;

        if (array_key_exists('value', $values)) {
            $value = $values['value'];
        }

        if (array_key_exists('policy', $values)) {
            $value = $values['policy'];
        }

        if (null !== $policy) {
            $value = $policy;
        }

        if (!\is_string($value)) {
            throw new RuntimeException('Exclusion policy value must be of string type.');
        }

        $value = strtoupper($value);

        if (self::NONE !== $value && self::ALL !== $value) {
            throw new RuntimeException('Exclusion policy must either be "ALL", or "NONE".');
        }

        $this->policy = $value;
    }
}
