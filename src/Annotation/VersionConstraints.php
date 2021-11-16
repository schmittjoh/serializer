<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

use Composer\Semver\Semver;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class VersionConstraints extends Version
{
    public function __construct($values = [], ?string $version = null)
    {
        if (!class_exists(Semver::class)) {
            throw new \LogicException(sprintf('composer/semver must be installed to use "%s".', self::class));
        }

        parent::__construct($values, $version);
    }
}
