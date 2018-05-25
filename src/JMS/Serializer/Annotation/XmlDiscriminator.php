<?php

namespace JMS\Serializer\Annotation;


/**
 * @Annotation
 * @Target("CLASS")
 */
class XmlDiscriminator
{
    /**
     * @var boolean
     */
    public $attribute = false;

    /**
     * @var boolean
     */
    public $cdata = true;

    /**
     * @var string
     */
    public $namespace;
}
