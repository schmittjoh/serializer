<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class XmlMap extends XmlCollection
{
    /**
     * @var string
     */
    public $keyAttribute = '_key';

    public function __construct(array $values = [], string $keyAttribute = '_key', string $entry = 'entry', bool $inline = false, ?string $namespace = null, bool $skipWhenEmpty = true)
    {
        if (array_key_exists('keyAttribute', $values)) {
            $keyAttribute = $values['keyAttribute'];
        }

        parent::__construct($values, $entry, $inline, $namespace, $skipWhenEmpty);
        $this->keyAttribute = $keyAttribute;
    }
}
