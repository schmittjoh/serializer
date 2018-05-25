<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
final class XmlValue
{
    /**
     * @var boolean
     */
    public $cdata = true;
}
