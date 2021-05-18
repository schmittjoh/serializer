<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class XmlNamespace
{
    /**
     * @Required
     * @var string|null
     */
    public $uri = null;

    /**
     * @var string
     */
    public $prefix = '';

    public function __construct(array $values = [], ?string $uri = null, string $prefix = '')
    {
        if ([] !== $values) {
            if (array_key_exists('uri', $values)) {
                $uri = $values['uri'];
            }

            if (array_key_exists('prefix', $values)) {
                $prefix = $values['prefix'];
            }
        }

        $this->uri = $uri;
        $this->prefix = $prefix;
    }
}
