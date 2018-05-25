<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class Accessor
{
    /**
     * @var string
     */
    public $getter;

    /**
     * @var string
     */
    public $setter;
}
