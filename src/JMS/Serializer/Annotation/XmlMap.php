<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
final class XmlMap extends XmlCollection
{
    /**
     * @var string
     */
    public $keyAttribute = '_key';
}
