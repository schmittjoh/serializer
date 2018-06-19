<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * Controls the order of properties in a class.
 *
 * @Annotation
 * @Target("CLASS")
 */
final class AccessorOrder
{
    /**
     * @Required
     * @var string
     */
    public $order;

    /** @var array<string> */
    public $custom = [];
}
