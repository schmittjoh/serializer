<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

use JMS\Serializer\Exception\RuntimeException;
use function is_string;
use function sprintf;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD", "ANNOTATION"})
 */
final class SerializedName
{
    public $name;

    public function __construct(array $values)
    {
        if (!isset($values['value']) || !is_string($values['value'])) {
            throw new RuntimeException(sprintf('"value" must be a string.'));
        }

        $this->name = $values['value'];
    }
}
