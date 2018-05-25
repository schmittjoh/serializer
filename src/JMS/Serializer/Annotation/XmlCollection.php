<?php

namespace JMS\Serializer\Annotation;

abstract class XmlCollection
{
    /**
     * @var string
     */
    public $entry = 'entry';

    /**
     * @var boolean
     */
    public $inline = false;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var boolean
     */
    public $skipWhenEmpty = true;
}
