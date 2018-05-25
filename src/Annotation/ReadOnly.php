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
     * @var boolean
     */
    public $readOnly = true;
}
