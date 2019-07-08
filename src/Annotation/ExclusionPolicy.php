<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

use JMS\Serializer\Exception\RuntimeException;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class ExclusionPolicy
{
    public const NONE = 'NONE';
    public const ALL = 'ALL';

    /**
     * @var string
     */
    public $policy;

    public function __construct(array $values)
    {
        $value = self::NONE;

        if (array_key_exists('value', $values)) {
            $value = $values['value'];
        }

        if (array_key_exists('policy', $values)) {
            $value = $values['policy'];
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
