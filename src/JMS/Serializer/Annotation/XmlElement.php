<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD","ANNOTATION"})
 */
final class XmlElement
{
    /**
     * @var boolean
     */
    public $cdata = true;

    /**
     * @var string
     */
    public $namespace;
}
