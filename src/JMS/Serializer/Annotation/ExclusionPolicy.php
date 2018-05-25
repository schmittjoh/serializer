<?php

namespace JMS\Serializer\Annotation;

use JMS\Serializer\Exception\RuntimeException;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class ExclusionPolicy
{
    const NONE = 'NONE';
    const ALL = 'ALL';

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
