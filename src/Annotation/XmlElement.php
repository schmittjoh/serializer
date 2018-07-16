<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD","ANNOTATION"})
 */
final class XmlElement
{
    /**
     * @var bool
     */
    public $cdata = true;

    /**
     * @var string
     */
    public $namespace;
}
