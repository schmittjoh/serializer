<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class AccessType
{
    /**
     * @Required
     * @var string
     */
    public $type;
}
