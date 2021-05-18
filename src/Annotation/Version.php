<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

abstract class Version
{
    /**
     * @Required
     * @var string|null
     */
    public $version = null;

    public function __construct(array $values = [], ?string $version = null)
    {
        if ([] !== $values) {
            if (array_key_exists('value', $values)) {
                $version = $values['value'];
            }

            if (array_key_exists('version', $values)) {
                $version = $values['version'];
            }
        }

        $this->version = $version;
    }
}
