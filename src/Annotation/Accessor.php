<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Accessor
{
    /** @var string */
    public $getter;

    /** @var string */
    public $setter;
}
