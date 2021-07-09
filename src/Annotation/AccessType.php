<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
final class AccessType
{
    /**
     * @Required
     * @var string|null
     */
    public $type;

    public function __construct(array $values = [], ?string $type = null)
    {
        if (array_key_exists('value', $values)) {
            $type = $values['value'];
        }

        if (array_key_exists('type', $values)) {
            $type = $values['type'];
        }

        $this->type = $type;
    }
}
