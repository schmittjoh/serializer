<?php

namespace JMS\Serializer\Annotation;

/**
 * Controls the order of properties in a class.
 *
 * @Annotation
 * @Target("CLASS")
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class AccessorOrder
{
    /**
     * @Required
     * @var string
     */
    public $order;

    /**
     * @var array<string>
     */
    public $custom = array();
}
