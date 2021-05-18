<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Accessor
{
    /**
     * @var string|null
     */
    public $getter = null;

    /**
     * @var string|null
     */
    public $setter = null;

    public function __construct(array $values = [], ?string $getter = null, ?string $setter = null)
    {
        if ([] !== $values) {
            if (array_key_exists('value', $values)) {
                $getter = $values['value'];
            }

            if (array_key_exists('getter', $values)) {
                $getter = $values['getter'];
            }

            if (array_key_exists('setter', $values)) {
                $setter = $values['setter'];
            }
        }

        $this->getter = $getter;
        $this->setter = $setter;
    }
}
