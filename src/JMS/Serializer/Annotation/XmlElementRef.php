<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
class XmlElementRef
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $type;
}