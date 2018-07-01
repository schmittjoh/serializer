<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"CLASS","PROPERTY"})
 */
final class ReadOnly
{
    /**
     * @var bool
     */
    public $readOnly = true;
}
