<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * Controls the order of properties in a class.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class AccessorOrder
{
    /**
     * @Required
     * @var string|null
     */
    public $order = null;

    /**
     * @var array<string>
     */
    public $custom = [];

    public function __construct(array $values = [], ?string $order = null, array $custom = [])
    {
        if ([] !== $values) {
            if (array_key_exists('value', $values)) {
                $order = $values['value'];
            }

            if (array_key_exists('order', $values)) {
                $order = $values['order'];
            }

            if (array_key_exists('custom', $values)) {
                $custom = $values['custom'];
            }
        }

        $this->order = $order;
        $this->custom = $custom;
    }
}
