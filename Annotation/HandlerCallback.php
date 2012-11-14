<?php

namespace JMS\SerializerBundle\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 *
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
