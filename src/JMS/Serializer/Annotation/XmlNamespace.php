<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class XmlNamespace
{
    /**
     * @Required
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $prefix = '';
}
