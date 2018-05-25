<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 * @deprecated
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class HandlerCallback
{
    /**
     * @Required
     * @var string
     */
    public $format;

    /**
     * @Required
     * @var string
     */
    public $direction;
}
