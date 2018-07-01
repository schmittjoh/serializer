<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class XmlDiscriminator
{
    /**
     * @var bool
     */
    public $attribute = false;

    /**
     * @var bool
     */
    public $cdata = true;

    /**
     * @var string
     */
    public $namespace;
}
