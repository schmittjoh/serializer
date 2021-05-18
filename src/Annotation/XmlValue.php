<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class XmlValue
{
    /**
     * @var bool
     */
    public $cdata = true;

    public function __construct(array $values = [], bool $cdata = true)
    {
        if ([] !== $values) {
            if (array_key_exists('value', $values)) {
                $cdata = $values['value'];
            }

            if (array_key_exists('cdata', $values)) {
                $cdata = $values['cdata'];
            }
        }

        $this->cdata = $cdata;
    }
}
