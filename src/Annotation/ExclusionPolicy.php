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
        if (!\is_string($values['value'])) {
            throw new RuntimeException('"value" must be a string.');
        }

        $this->policy = strtoupper($values['value']);

        if (self::NONE !== $this->policy && self::ALL !== $this->policy) {
            throw new RuntimeException('Exclusion policy must either be "ALL", or "NONE".');
        }
    }
}
